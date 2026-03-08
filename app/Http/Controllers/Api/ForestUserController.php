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
     * Get recommended users for friendship
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function recommendations(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }
        
        $perPage = $request->get('per_page', 10);
        
        // Get users with same animal type or compatible types, excluding current user and friends
        $recommendations = User::where('id', '!=', $user->id)
            ->whereDoesntHave('friends', function ($query) use ($user) {
                $query->where(function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                      ->orWhere('friend_id', $user->id);
                });
            })
            ->where(function ($query) use ($user) {
                // Same animal type or null (any)
                $query->where('animal_type_id', $user->animal_type_id)
                      ->orWhereNull('animal_type_id');
            })
            ->with(['animalType'])
            ->paginate($perPage);
        
        return response()->json([
            'success' => true,
            'data' => $recommendations->map(function ($recommendedUser) {
                return [
                    'id' => $recommendedUser->id,
                    'name' => $recommendedUser->name,
                    'animal_type' => $recommendedUser->animalType ? [
                        'id' => $recommendedUser->animalType->id,
                        'name' => $recommendedUser->animalType->name,
                        'image' => $recommendedUser->animalType->image,
                    ] : null,
                    'gender' => $recommendedUser->gender,
                    'avatar' => $recommendedUser->avatar,
                ];
            }),
            'pagination' => [
                'current_page' => $recommendations->currentPage(),
                'per_page' => $recommendations->perPage(),
                'total' => $recommendations->total(),
                'last_page' => $recommendations->lastPage(),
            ]
        ], 200);
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