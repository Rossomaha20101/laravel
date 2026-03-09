<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Laravel\Sanctum\HasApiTokens;

class ForestUser extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'nickname',
        'email',
        'password',
        'animal_type_id',
        'gender',
        'birth_date',
        'best_friend_name',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'birth_date' => 'date',
    ];

    /**
     * Связь: Тип животного
     */
    public function animalType(): BelongsTo
    {
        return $this->belongsTo(AnimalType::class, 'animal_type_id');
    }

    /**
     * Друзья пользователя (статус 'accepted')
     * Возвращает коллекцию моделей ForestUser
     * 
     * @return Collection<int, ForestUser>
     */
    public function getFriendsList(): Collection
    {
        // 1. Находим все подтверждённые записи дружбы
        $friendships = ForestFriendship::where('status', 'accepted')
            ->where(function($q) {
                $q->where('forest_user_id', $this->id)
                ->orWhere('friend_id', $this->id);
            })
            ->get();
        
        // 2. Собираем ID друзей
        $friendIds = $friendships->map(function($f) {
            return $f->forest_user_id == $this->id ? $f->friend_id : $f->forest_user_id;
        });
        
        // 3. Возвращаем пустую коллекцию, если друзей нет
        if ($friendIds->isEmpty()) {
            return collect();
        }
        
        // 4. Возвращаем модели друзей
        return ForestUser::whereIn('id', $friendIds)
            ->with('animalType')
            ->get();
    }

    /**
     * Исходящие заявки на дружбу (я отправил)
     */
    public function sentFriendRequests(): HasMany
    {
        return $this->hasMany(ForestFriendship::class, 'forest_user_id')
                    ->where('status', 'pending');
    }

    /**
     * Входящие заявки на дружбу (мне отправили)
     */
    public function receivedFriendRequests(): HasMany
    {
        return $this->hasMany(ForestFriendship::class, 'friend_id')
                    ->where('status', 'pending');
    }

    /**
     * Проверка: является ли $otherUser моим другом
     */
    public function isFriendsWith(ForestUser $otherUser): bool
    {
        return ForestFriendship::where('status', 'accepted')
            ->where(function($q) use ($otherUser) {
                $q->where('forest_user_id', $this->id)
                ->where('friend_id', $otherUser->id);
            })
            ->orWhere(function($q) use ($otherUser) {
                $q->where('forest_user_id', $otherUser->id)
                ->where('friend_id', $this->id);
            })
            ->exists();
    }

    /**
     * Отправить заявку в друзья
     */
    public function sendFriendRequest(ForestUser $friend): ForestFriendship
    {
        return ForestFriendship::create([
            'forest_user_id' => $this->id,
            'friend_id' => $friend->id,
            'status' => 'pending',
        ]);
    }

    /**
     * Принять заявку в друзья
     */
    public function acceptFriendRequest(ForestUser $friend): bool
    {
        $friendship = ForestFriendship::where('forest_user_id', $friend->id)
            ->where('friend_id', $this->id)
            ->where('status', 'pending')
            ->first();
            
        if ($friendship) {
            $friendship->update(['status' => 'accepted']);
            return true;
        }
        return false;
    }

    /**
     * Отклонить/заблокировать заявку
     */
    public function rejectFriendRequest(ForestUser $friend): bool
    {
        $friendship = ForestFriendship::where('forest_user_id', $friend->id)
            ->where('friend_id', $this->id)
            ->where('status', 'pending')
            ->first();
            
        if ($friendship) {
            $friendship->update(['status' => 'blocked']);
            return true;
        }
        return false;
    }

    /**
     * Отправленные сообщения (личные)
     */
    public function sentMessages(): HasMany
    {
        return $this->hasMany(ForestMessage::class, 'sender_id')
                    ->where('type', 'personal');
    }

    /**
     * Полученные сообщения (личные)
     */
    public function receivedMessages(): HasMany
    {
        return $this->hasMany(ForestMessage::class, 'recipient_id')
                    ->where('type', 'personal');
    }

    /**
     * Все сообщения (отправленные и полученные)
     */
    public function allMessages()
    {
        return ForestMessage::where('type', 'personal')
            ->where(function($q) {
                $q->where('sender_id', $this->id)
                  ->orWhere('recipient_id', $this->id);
            })
            ->orderBy('created_at', 'desc');
    }

    /**
     * Группы, в которых состоит пользователь
     */
    public function messageGroups(): BelongsToMany
    {
        return $this->belongsToMany(ForestMessageGroup::class, 'forest_message_group_users', 'user_id', 'group_id')
                    ->withTimestamps();
    }

    /**
     * Получить диалог с конкретным пользователем
     */
    public function getConversationWith(ForestUser $otherUser, int $limit = 50)
    {
        return ForestMessage::where('type', 'personal')
            ->where(function($q) use ($otherUser) {
                $q->where(function($q2) use ($otherUser) {
                        $q2->where('sender_id', $this->id)
                           ->where('recipient_id', $otherUser->id);
                    })
                    ->orWhere(function($q2) use ($otherUser) {
                        $q2->where('sender_id', $otherUser->id)
                           ->where('recipient_id', $this->id);
                    });
            })
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->reverse()
            ->values();
    }

}