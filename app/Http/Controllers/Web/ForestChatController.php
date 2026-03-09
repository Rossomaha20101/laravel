<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ForestMessage;
use App\Models\ForestMessageGroup;
use App\Models\ForestUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForestChatController extends Controller
{
    /**
     * Список диалогов (главная страница чата)
     * GET /forest/chat
     */
    public function index(Request $request)
    {
        $user = Auth::guard('forest')->user();
        
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
        
        // Сортируем по дате последнего сообщения
        usort($conversations, function($a, $b) {
            return strtotime($b['last_message']->created_at) - strtotime($a['last_message']->created_at);
        });
        
        return view('forest.chat.index', [
            'conversations' => $conversations,
            'user' => $user,
        ]);
    }

    /**
     * Переписка с конкретным пользователем
     * GET /forest/chat/{id}
     */
    public function conversation(Request $request, $id)
    {
        $user = Auth::guard('forest')->user();
        $otherUser = ForestUser::find($id);
        
        if (!$otherUser) {
            abort(404, 'Пользователь не найден');
        }
        
        // Получаем историю переписки
        $messages = ForestMessage::where('type', 'personal')
            ->where(function($q) use ($user, $id) {
                $q->where(function($q2) use ($user, $id) {
                        $q2->where('sender_id', $user->id)
                           ->where('recipient_id', $id);
                    })
                    ->orWhere(function($q2) use ($user, $id) {
                        $q2->where('sender_id', $id)
                           ->where('recipient_id', $user->id);
                    });
            })
            ->with('sender', 'recipient')
            ->orderBy('created_at', 'asc')
            ->paginate(50);
        
        return view('forest.chat.conversation', [
            'otherUser' => $otherUser,
            'messages' => $messages,
            'user' => $user,
        ]);
    }

    /**
     * Отправить сообщение (web-форма)
     * POST /forest/chat/{id}/send
     */
    public function send(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string|max:128',
        ]);
        
        $user = Auth::guard('forest')->user();
        $otherUser = ForestUser::find($id);
        
        if (!$otherUser) {
            return back()->with('error', 'Пользователь не найден');
        }
        
        ForestMessage::create([
            'sender_id' => $user->id,
            'recipient_id' => $otherUser->id,
            'type' => 'personal',
            'content' => $request->content,
        ]);
        
        return back();
    }

    /**
     * Мои групповые чаты
     * GET /forest/chat/groups
     */
    public function groups(Request $request)
    {
        $user = Auth::guard('forest')->user();
        $groups = $user->messageGroups()->with('users', 'creator')->get();
        
        return view('forest.chat.groups', [
            'groups' => $groups,
            'user' => $user,
        ]);
    }

    /**
     * Сообщения в групповом чате
     * GET /forest/chat/groups/{id}
     */
    public function groupChat(Request $request, $id)
    {
        $user = Auth::guard('forest')->user();
        $group = ForestMessageGroup::with('users', 'creator')->find($id);
        
        if (!$group) {
            abort(404, 'Группа не найдена');
        }
        
        if (!$group->isMember($user)) {
            abort(403, 'Вы не являетесь участником этой группы');
        }
        
        $messages = ForestMessage::where('group_id', $group->id)
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->paginate(50);
        
        return view('forest.chat.group', [
            'group' => $group,
            'messages' => $messages,
            'user' => $user,
        ]);
    }

    /**
     * Отправить сообщение в группу (web-форма)
     * POST /forest/chat/groups/{id}/send
     */
    public function sendToGroup(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string|max:128',
        ]);
        
        $user = Auth::guard('forest')->user();
        $group = ForestMessageGroup::find($id);
        
        if (!$group) {
            return back()->with('error', 'Группа не найдена');
        }
        
        if (!$group->isMember($user)) {
            return back()->with('error', 'Вы не являетесь участником этой группы');
        }
        
        ForestMessage::create([
            'sender_id' => $user->id,
            'group_id' => $group->id,
            'type' => 'group',
            'content' => $request->content,
        ]);
        
        return back();
    }

    /**
     * Создать групповой чат
     * POST /forest/chat/groups/create
     */
    public function createGroup(Request $request)
    {
        $request->validate([
            'name' => 'nullable|string|max:100',
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'integer|exists:forest_users,id',
        ]);
        
        $user = Auth::guard('forest')->user();
        
        $group = ForestMessageGroup::create([
            'name' => $request->name ?? 'Групповой чат',
            'created_by' => $user->id,
        ]);
        
        // Добавляем создателя
        $group->addMember($user);
        
        // Добавляем выбранных друзей
        foreach ($request->user_ids as $userId) {
            if ($userId != $user->id) {
                $member = ForestUser::find($userId);
                if ($member) {
                    $group->addMember($member);
                }
            }
        }
        
        return redirect()->route('forest.chat.group', $group->id)
            ->with('success', '🎉 Групповой чат создан!');
    }
}