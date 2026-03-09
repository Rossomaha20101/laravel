<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ForestMessage;
use App\Models\ForestMessageGroup;
use App\Models\ForestUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForestMessageController extends Controller
{
    /**
     * Получить список диалогов (последние сообщения с каждым пользователем)
     * GET /api/messages/conversations
     */
    public function conversations(Request $request)
    {
        $user = $request->user();
        
        // Получаем все сообщения, где пользователь участвует
        $messages = ForestMessage::where('type', 'personal')
            ->where(function($q) use ($user) {
                $q->where('sender_id', $user->id)
                  ->orWhere('recipient_id', $user->id);
            })
            ->with('sender', 'recipient')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Группируем по собеседнику
        $conversations = [];
        foreach ($messages as $message) {
            $otherUser = $message->sender_id === $user->id ? $message->recipient : $message->sender;
            if ($otherUser) {
                $conversations[$otherUser->id] = [
                    'user' => $otherUser,
                    'last_message' => $message,
                    'unread_count' => ForestMessage::where('sender_id', $otherUser->id)
                        ->where('recipient_id', $user->id)
                        ->where('created_at', '>', $user->last_read_at ?? '1970-01-01')
                        ->count(),
                ];
            }
        }
        
        return response()->json([
            'success' => true,
            'data' => array_values($conversations),
        ]);
    }

    /**
     * Получить историю переписки с пользователем
     * GET /api/messages?with={user_id}
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $withId = $request->query('with');
        
        if (!$withId) {
            return response()->json([
                'success' => false,
                'message' => 'Параметр ?with={user_id} обязателен',
            ], 422);
        }
        
        $otherUser = ForestUser::find($withId);
        if (!$otherUser) {
            return response()->json([
                'success' => false,
                'message' => 'Пользователь не найден',
            ], 404);
        }
        
        $messages = ForestMessage::where('type', 'personal')
            ->where(function($q) use ($user, $withId) {
                $q->where(function($q2) use ($user, $withId) {
                        $q2->where('sender_id', $user->id)
                           ->where('recipient_id', $withId);
                    })
                    ->orWhere(function($q2) use ($user, $withId) {
                        $q2->where('sender_id', $withId)
                           ->where('recipient_id', $user->id);
                    });
            })
            ->with('sender', 'recipient')
            ->orderBy('created_at', 'asc')
            ->paginate(50);
        
        return response()->json([
            'success' => true,
            'data' => $messages,
            'other_user' => $otherUser,
        ]);
    }

    /**
     * Отправить личное сообщение
     * POST /api/messages
     */
    public function store(Request $request)
    {
        $request->validate([
            'recipient_id' => 'required|integer|exists:forest_users,id',
            'content' => 'required|string|max:128', // ← Максимум 128 символов по ТЗ
        ]);
        
        $user = $request->user();
        
        if ($user->id == $request->recipient_id) {
            return response()->json([
                'success' => false,
                'message' => 'Нельзя отправить сообщение самому себе',
            ], 422);
        }
        
        $message = ForestMessage::create([
            'sender_id' => $user->id,
            'recipient_id' => $request->recipient_id,
            'type' => 'personal',
            'content' => $request->content,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Сообщение отправлено',
            'data' => $message->load('sender', 'recipient'),
        ], 201);
    }

    /**
     * Создать групповой чат
     * POST /api/messages/groups
     */
    public function createGroup(Request $request)
    {
        $request->validate([
            'name' => 'nullable|string|max:100',
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'integer|exists:forest_users,id',
        ]);
        
        $user = $request->user();
        
        $group = ForestMessageGroup::create([
            'name' => $request->name ?? 'Групповой чат',
            'created_by' => $user->id,
        ]);
        
        // Добавляем создателя и участников
        $group->addMember($user);
        foreach ($request->user_ids as $userId) {
            $group->addMember(ForestUser::find($userId));
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Групповой чат создан',
            'data' => $group->load('users'),
        ], 201);
    }

    /**
     * Отправить сообщение в группу
     * POST /api/messages/groups/{id}
     */
    public function sendToGroup(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string|max:128',
        ]);
        
        $user = $request->user();
        $group = ForestMessageGroup::find($id);
        
        if (!$group) {
            return response()->json([
                'success' => false,
                'message' => 'Группа не найдена',
            ], 404);
        }
        
        if (!$group->isMember($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Вы не являетесь участником этой группы',
            ], 403);
        }
        
        $message = ForestMessage::create([
            'sender_id' => $user->id,
            'group_id' => $group->id,
            'type' => 'group',
            'content' => $request->content,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Сообщение отправлено в группу',
            'data' => $message->load('sender', 'group'),
        ], 201);
    }

    /**
     * Получить сообщения из группы
     * GET /api/messages/groups/{id}
     */
    public function getGroupMessages(Request $request, $id)
    {
        $user = $request->user();
        $group = ForestMessageGroup::find($id);
        
        if (!$group) {
            return response()->json([
                'success' => false,
                'message' => 'Группа не найдена',
            ], 404);
        }
        
        if (!$group->isMember($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Вы не являетесь участником этой группы',
            ], 403);
        }
        
        $messages = ForestMessage::where('group_id', $group->id)
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->paginate(50);
        
        return response()->json([
            'success' => true,
            'data' => $messages,
            'group' => $group->load('users'),
        ]);
    }

    /**
     * Получить свои групповые чаты
     * GET /api/messages/groups
     */
    public function myGroups(Request $request)
    {
        $user = $request->user();
        
        $groups = $user->messageGroups()->with('users', 'creator')->get();
        
        return response()->json([
            'success' => true,
            'data' => $groups,
        ]);
    }
}