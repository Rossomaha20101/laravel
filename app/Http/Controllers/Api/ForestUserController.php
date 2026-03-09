<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AnimalType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ForestUserController extends Controller
{
    /**
     * Get current authenticated user profile
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'animal_type' => $user->animalType ? [
                    'id' => $user->animalType->id,
                    'name' => $user->animalType->name,
                    'image' => $user->animalType->image,
                ] : null,
                'gender' => $user->gender,
                'birth_date' => $user->birth_date,
                'best_friend_name' => $user->best_friend_name,
                'avatar' => $user->avatar,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ]
        ], 200);
    }

    /**
     * Получить рекомендации друзей (алгоритм из 4 уровней)
     * GET /api/recommendations
     */
    public function recommendations(Request $request)
    {
        $user = $request->user();
        $bestFriendName = mb_strtolower(trim($user->best_friend_name));
        
        // Исключаем: самого пользователя, уже друзей и тех, кому уже отправлена заявка
        $friendIds = $user->getFriendsList()->pluck('id')->toArray();
        $pendingSent = \App\Models\ForestFriendship::where('forest_user_id', $user->id)
            ->where('status', 'pending')
            ->pluck('friend_id')
            ->toArray();
        $pendingReceived = \App\Models\ForestFriendship::where('friend_id', $user->id)
            ->where('status', 'pending')
            ->pluck('forest_user_id')
            ->toArray();
        
        $excludeIds = array_unique(array_merge(
            [$user->id],
            $friendIds,
            $pendingSent,
            $pendingReceived
        ));

        $results = [];
        $level = 0;
        $rule = '';

        // === УРОВЕНЬ 1: Совпадение по ИМЕНИ с лучшим другом ===
        $level1 = $this->findSimilarUsers('name', $bestFriendName, $excludeIds);
        
        if (count($level1) >= 10) {
            return $this->formatResponse(array_slice($level1, 0, 10), 1, 'Совпадение имени с именем лучшего друга');
        }
        $results = array_merge($results, $level1);
        $excludeIds = array_merge($excludeIds, array_column($level1, 'id'));

        // === УРОВЕНЬ 2: Совпадение по ПРОЗВИЩУ (nickname) ===
        if (count($results) < 10) {
            $level2 = $this->findSimilarUsers('nickname', $bestFriendName, $excludeIds);
            
            if (count($results) + count($level2) >= 10) {
                $needed = 10 - count($results);
                $results = array_merge($results, array_slice($level2, 0, $needed));
                return $this->formatResponse($results, 2, 'Совпадение прозвища с именем лучшего друга');
            }
            $results = array_merge($results, $level2);
            $excludeIds = array_merge($excludeIds, array_column($level2, 'id'));
        }

        // === УРОВЕНЬ 3: Тот же вид + противоположный пол ===
        if (count($results) < 10) {
            $oppositeGender = $user->gender === 'M' ? 'F' : 'M';
            
            $level3 = \App\Models\ForestUser::whereNotIn('id', $excludeIds)
                ->where('animal_type_id', $user->animal_type_id)
                ->where('gender', $oppositeGender)
                ->limit(10 - count($results))
                ->get()
                ->toArray();
            
            if (count($results) + count($level3) >= 10) {
                $needed = 10 - count($results);
                $results = array_merge($results, array_slice($level3, 0, $needed));
                return $this->formatResponse($results, 3, 'Тот же вид животного + противоположный пол');
            }
            $results = array_merge($results, $level3);
            $excludeIds = array_merge($excludeIds, array_column($level3, 'id'));
        }

        // === УРОВЕНЬ 4: Просто тот же вид ===
        if (count($results) < 10) {
            $level4 = \App\Models\ForestUser::whereNotIn('id', $excludeIds)
                ->where('animal_type_id', $user->animal_type_id)
                ->limit(10 - count($results))
                ->get()
                ->toArray();
            
            $results = array_merge($results, array_slice($level4, 0, 10 - count($results)));
        }

        return $this->formatResponse($results, 4, 'Тот же вид животного');
    }

    /**
     * Вспомогательный метод: поиск пользователей со схожим именем или ником
     * Использует Levenshtein для расчёта "близости" слов
     */
    private function findSimilarUsers(string $column, string $searchTerm, array $excludeIds): array
    {
        if (empty($searchTerm)) {
            return [];
        }

        // Получаем кандидатов через LIKE для производительности
        $candidates = \App\Models\ForestUser::whereNotIn('id', $excludeIds)
            ->whereNotNull($column)
            ->where($column, 'LIKE', "%{$searchTerm}%")
            ->get();

        $scored = [];

        foreach ($candidates as $candidate) {
            $value = mb_strtolower($candidate->$column);
            
            // Точное совпадение - максимальный приоритет
            if ($value === $searchTerm) {
                $scored[] = [
                    'id' => $candidate->id,
                    'name' => $candidate->name,
                    'nickname' => $candidate->nickname,
                    'animal_type_id' => $candidate->animal_type_id,
                    'gender' => $candidate->gender,
                    'birth_date' => $candidate->birth_date,
                    'best_friend_name' => $candidate->best_friend_name,
                    'email' => $candidate->email,
                    'animalType' => $candidate->animalType,
                    'similarity' => 1.0,
                ];
            } else {
                // Расчёт расстояния Левенштейна
                $distance = levenshtein($value, $searchTerm);
                $maxLen = max(mb_strlen($value), mb_strlen($searchTerm));
                
                if ($maxLen > 0) {
                    $similarity = 1.0 - ($distance / $maxLen);
                    
                    // Берём только достаточно похожие (порог 0.6)
                    if ($similarity >= 0.6) {
                        $scored[] = [
                            'id' => $candidate->id,
                            'name' => $candidate->name,
                            'nickname' => $candidate->nickname,
                            'animal_type_id' => $candidate->animal_type_id,
                            'gender' => $candidate->gender,
                            'birth_date' => $candidate->birth_date,
                            'best_friend_name' => $candidate->best_friend_name,
                            'email' => $candidate->email,
                            'animalType' => $candidate->animalType,
                            'similarity' => $similarity,
                        ];
                    }
                }
            }
        }

        // Сортируем по убыванию схожести
        usort($scored, fn($a, $b) => $b['similarity'] <=> $a['similarity']);
        
        return $scored;
    }

    /**
     * Форматирует ответ JSON
     */
    private function formatResponse(array $users, int $level, string $rule): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $users,
            'meta' => [
                'count' => count($users),
                'level_applied' => $level,
                'rule' => $rule,
                'best_friend_name' => \Illuminate\Support\Facades\Auth::guard('sanctum')->user()->best_friend_name,
            ]
        ]);
    }
    
    /**
     * Update user profile
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }
        
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'animal_type_id' => ['sometimes', 'exists:animal_types,id'],
            'gender' => ['sometimes', Rule::in(['M', 'F', 'O'])],
            'birth_date' => ['sometimes', 'date', 'before:today'],
            'best_friend_name' => ['sometimes', 'string', 'max:255'],
            'avatar' => ['sometimes', 'image', 'max:2048'],
            'password' => ['sometimes', 'string', 'min:8', 'confirmed'],
        ]);
        
        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }
        
        // Handle password separately
        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }
        
        $user->update($validated);
        
        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'animal_type' => $user->animalType ? [
                    'id' => $user->animalType->id,
                    'name' => $user->animalType->name,
                ] : null,
                'gender' => $user->gender,
                'birth_date' => $user->birth_date,
                'best_friend_name' => $user->best_friend_name,
                'avatar' => $user->avatar,
            ]
        ], 200);
    }
    
    /**
     * Get user by ID (for viewing other users' public profiles)
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $currentUser = Auth::user();
        
        if (!$currentUser) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }
        
        $user = User::with(['animalType'])->find($id);
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }
        
        // Return only public fields
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'animal_type' => $user->animalType ? [
                    'id' => $user->animalType->id,
                    'name' => $user->animalType->name,
                    'image' => $user->animalType->image,
                ] : null,
                'gender' => $user->gender,
                'birth_date' => $user->birth_date,
                'best_friend_name' => $user->best_friend_name,
                'avatar' => $user->avatar,
                'is_friend' => $currentUser->friends()->where('friend_id', $id)->exists() 
                           || $currentUser->friendsOf()->where('user_id', $id)->exists(),
            ]
        ], 200);
    }
    
    /**
     * Delete user account
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy()
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }
        
        // Soft delete or hard delete based on your needs
        $user->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Account deleted successfully'
        ], 200);
    }
}