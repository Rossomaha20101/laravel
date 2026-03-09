<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ForestUser;
use App\Models\ForestFriendship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForestFriendController extends Controller
{
    /**
     * Получить список друзей текущего пользователя
     * GET /api/friends
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Получаем все подтверждённые дружбы, где участвует текущий пользователь
        $friendships = ForestFriendship::accepted()
            ->forUser($user->id)
            ->with('user', 'friend')
            ->get();
        
        // Формируем список друзей (исключая самого пользователя)
        $friends = $friendships->map(function ($friendship) use ($user) {
            return $friendship->getOtherUser($user);
        })->filter(); // Убираем null
        
        return response()->json([
            'success' => true,
            'data' => $friends,
            'count' => $friends->count(),
        ]);
    }

    /**
     * Добавить пользователя в друзья (отправить заявку)
     * POST /api/friends/{id}
     */
    public function add(Request $request, $id)
    {
        $user = $request->user();
        
        // Нельзя добавить себя в друзья
        if ($user->id == $id) {
            return response()->json([
                'success' => false,
                'message' => 'Нельзя добавить себя в друзья',
            ], 422);
        }
        
        // Проверяем, существует ли пользователь
        $friend = ForestUser::find($id);
        if (!$friend) {
            return response()->json([
                'success' => false,
                'message' => 'Пользователь не найден',
            ], 404);
        }
        
        // Проверяем, не друзья ли они уже
        $exists = ForestFriendship::forUser($user->id)
            ->forUser($id)
            ->whereIn('status', ['pending', 'accepted'])
            ->exists();
            
        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Заявка уже отправлена или вы уже друзья',
            ], 422);
        }
        
        // Создаём заявку на дружбу
        $friendship = ForestFriendship::create([
            'forest_user_id' => $user->id,
            'friend_id' => $id,
            'status' => 'pending',
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Заявка в друзья отправлена',
            'data' => [
                'friendship_id' => $friendship->id,
                'status' => $friendship->status,
                'friend' => $friend,
            ],
        ], 201);
    }

    /**
     * Удалить пользователя из друзей (или отменить заявку)
     * DELETE /api/friends/{id}
     */
    public function remove(Request $request, $id)
    {
        $user = $request->user();
        
        // Ищем запись в любом направлении
        $friendship = ForestFriendship::where(function($q) use ($user, $id) {
                $q->where('forest_user_id', $user->id)
                  ->where('friend_id', $id);
            })
            ->orWhere(function($q) use ($user, $id) {
                $q->where('forest_user_id', $id)
                  ->where('friend_id', $user->id);
            })
            ->first();
        
        if (!$friendship) {
            return response()->json([
                'success' => false,
                'message' => 'Дружба не найдена',
            ], 404);
        }
        
        // Просто удаляем запись
        $friendship->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Пользователь удалён из друзей',
        ]);
    }

    /**
     * Принять входящую заявку в друзья
     * POST /api/friends/{id}/accept
     */
    public function accept(Request $request, $id)
    {
        $user = $request->user();
        
        // Ищем входящую заявку: кто-то отправил нам
        $friendship = ForestFriendship::where('forest_user_id', $id)
            ->where('friend_id', $user->id)
            ->where('status', 'pending')
            ->first();
        
        if (!$friendship) {
            return response()->json([
                'success' => false,
                'message' => 'Заявка не найдена или уже обработана',
            ], 404);
        }
        
        // Принимаем заявку
        $friendship->update(['status' => 'accepted']);
        
        return response()->json([
            'success' => true,
            'message' => 'Заявка принята! Теперь вы друзья 🎉',
            'data' => [
                'friend' => ForestUser::find($id),
            ],
        ]);
    }

    /**
     * Получить входящие заявки на дружбу
     * GET /api/friends/requests
     */
    public function requests(Request $request)
    {
        $user = $request->user();
        
        // Входящие заявки: кто-то отправил заявку нам
        $requests = ForestFriendship::where('friend_id', $user->id)
            ->where('status', 'pending')
            ->with('user')
            ->get();
        
        $senders = $requests->map(fn($r) => $r->user);
        
        return response()->json([
            'success' => true,
            'data' => $senders,
            'count' => $senders->count(),
        ]);
    }

    // =================================================================
    // === МЕТОДЫ ДЛЯ ВЕБ-ИНТЕРФЕЙСА ===
    // =================================================================

    /**
     * Показать страницу "Мои друзья"
     * GET /forest/friends
     */
    public function friendsPage(Request $request)
    {
        $user = \Illuminate\Support\Facades\Auth::guard('forest')->user();
        
        $friends = \App\Models\ForestFriendship::accepted()
            ->forUser($user->id)
            ->with('user', 'friend')
            ->get()
            ->map(fn($f) => $f->getOtherUser($user));
        
        return view('forest.friends.index', compact('friends', 'user'));
    }

    /**
     * Показать страницу "Входящие заявки"
     * GET /forest/friends/requests
     */
    public function requestsPage(Request $request)
    {
        $user = \Illuminate\Support\Facades\Auth::guard('forest')->user();
        
        $requests = \App\Models\ForestFriendship::where('friend_id', $user->id)
            ->where('status', 'pending')
            ->with('user')
            ->get()
            ->map(fn($r) => $r->user);
        
        return view('forest.friends.requests', compact('requests', 'user'));
    }

    /**
     * Обработать отправку заявки (веб-форма)
     * POST /forest/friends/send
     */
    public function sendRequestWeb(Request $request)
    {
        $request->validate([
            'friend_id' => 'required|integer|exists:forest_users,id',
        ]);
        
        $user = \Illuminate\Support\Facades\Auth::guard('forest')->user();
        $friendId = $request->friend_id;
        
        if ($user->id == $friendId) {
            return back()->with('error', 'Нельзя добавить себя в друзья');
        }
        
        $exists = \App\Models\ForestFriendship::forUser($user->id)
            ->forUser($friendId)
            ->whereIn('status', ['pending', 'accepted'])
            ->exists();
            
        if ($exists) {
            return back()->with('error', 'Заявка уже отправлена или вы уже друзья');
        }
        
        \App\Models\ForestFriendship::create([
            'forest_user_id' => $user->id,
            'friend_id' => $friendId,
            'status' => 'pending',
        ]);
        
        return back()->with('success', '✅ Заявка отправлена!');
    }

    /**
     * Принять заявку (веб-форма)
     * POST /forest/friends/accept/{id}
     */
    public function acceptRequestWeb(Request $request, $id)
    {
        $user = \Illuminate\Support\Facades\Auth::guard('forest')->user();
        
        $friendship = \App\Models\ForestFriendship::where('forest_user_id', $id)
            ->where('friend_id', $user->id)
            ->where('status', 'pending')
            ->first();
        
        if (!$friendship) {
            return back()->with('error', 'Заявка не найдена');
        }
        
        $friendship->update(['status' => 'accepted']);
        
        return back()->with('success', '🎉 Заявка принята! Теперь вы друзья');
    }

    /**
     * Отклонить заявку (веб-форма)
     * POST /forest/friends/reject/{id}
     */
    public function rejectRequestWeb(Request $request, $id)
    {
        $user = \Illuminate\Support\Facades\Auth::guard('forest')->user();
        
        $friendship = \App\Models\ForestFriendship::where('forest_user_id', $id)
            ->where('friend_id', $user->id)
            ->where('status', 'pending')
            ->first();
        
        if ($friendship) {
            $friendship->update(['status' => 'rejected']);
        }
        
        return back()->with('success', 'Заявка отклонена');
    }

    /**
     * Удалить из друзей (веб-форма)
     * Просто удаляет запись о дружбе — без блокировки
     * POST /forest/friends/remove/{id}
     */
    public function removeFriendWeb(Request $request, $id)
    {
        $user = \Illuminate\Support\Facades\Auth::guard('forest')->user();
        
        // Ищем запись дружбы в ЛЮБОМ направлении:
        // 1. Мы были инициатором: forest_user_id = мы, friend_id = друг
        // 2. Друг был инициатором: forest_user_id = друг, friend_id = мы
        $friendship = ForestFriendship::where(function($q) use ($user, $id) {
                $q->where('forest_user_id', $user->id)
                  ->where('friend_id', $id);
            })
            ->orWhere(function($q) use ($user, $id) {
                $q->where('forest_user_id', $id)
                  ->where('friend_id', $user->id);
            })
            ->first();
        
        if (!$friendship) {
            return back()->with('error', 'Дружба не найдена');
        }
        
        // Просто удаляем запись — связь разорвана
        $friendship->delete();
        
        return back()->with('success', '👋 Пользователь удалён из друзей');
    }

    /**
     * Страница поиска пользователей для добавления в друзья
     * GET /forest/friends/search
     */
    public function searchPage(Request $request)
    {
        $user = \Illuminate\Support\Facades\Auth::guard('forest')->user();
        $query = $request->input('q');
        
        $users = \App\Models\ForestUser::where('id', '!=', $user->id)
            ->when($query, function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('nickname', 'LIKE', "%{$query}%");
            })
            ->limit(20)
            ->get();
        
        // Добавляем информацию о статусе дружбы
        $users->each(function($u) use ($user) {
            // Ищем заявку в ОБОИХ направлениях
            $friendship = \App\Models\ForestFriendship::where(function($q) use ($user, $u) {
                    $q->where('forest_user_id', $user->id)
                      ->where('friend_id', $u->id);
                })
                ->orWhere(function($q) use ($user, $u) {
                    $q->where('forest_user_id', $u->id)
                      ->where('friend_id', $user->id);
                })
                ->first();
            
            if ($friendship) {
                $u->friendship_status = $friendship->status;
                
                // Определяем, кто отправил заявку
                if ($friendship->forest_user_id === $user->id) {
                    // Я отправил заявку этому пользователю
                    $u->friendship_direction = 'outgoing';
                } else {
                    // Этот пользователь отправил заявку мне
                    $u->friendship_direction = 'incoming';
                }
            } else {
                $u->friendship_status = null;
                $u->friendship_direction = null;
            }
        });
        
        return view('forest.friends.search', compact('users', 'user', 'query'));
    }

    /**
     * Страница рекомендаций друзей
     * GET /forest/recommendations
     */
    public function recommendationsPage(Request $request)
    {
        $user = \Illuminate\Support\Facades\Auth::guard('forest')->user();
        
        // Вызываем тот же метод, что и для API
        $apiRequest = Request::create('/api/recommendations', 'GET');
        $apiRequest->setUserResolver(fn() => $user);
        
        $response = app()->call('App\Http\Controllers\Api\ForestUserController@recommendations', ['request' => $apiRequest]);
        $data = json_decode($response->getContent(), true);
        
        return view('forest.friends.recommendations', [
            'recommendations' => $data['data'] ?? [],
            'meta' => $data['meta'] ?? [],
            'user' => $user,
        ]);
    }
}