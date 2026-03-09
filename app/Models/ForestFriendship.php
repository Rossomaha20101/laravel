<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class ForestFriendship extends Model
{
    protected $table = 'forest_friendships';

    protected $fillable = [
        'forest_user_id',  // ✅ Кто отправил заявку
        'friend_id',       // ✅ Кому отправили
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // === Константы статусов ===
    public const STATUS_PENDING = 'pending';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_BLOCKED = 'blocked';

    /**
     * Получение списка всех возможных статусов
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_ACCEPTED,
            self::STATUS_REJECTED,
            self::STATUS_BLOCKED,
        ];
    }

    /**
     * Связь: Инициатор заявки (кто отправил)
     */
    public function user(): BelongsTo  // ← Переименовали для ясности
    {
        return $this->belongsTo(ForestUser::class, 'forest_user_id');  // ← Исправлено!
    }

    /**
     * Связь: Друг (кому отправили заявку)
     */
    public function friend(): BelongsTo  // ← Переименовали для ясности
    {
        return $this->belongsTo(ForestUser::class, 'friend_id');  // ← Исправлено!
    }

    /**
     * Получить другого пользователя (не текущего)
     * Удобно для отображения в списке друзей
     */
    public function getOtherUser(ForestUser $currentUser): ?ForestUser
    {
        if ($this->forest_user_id === $currentUser->id) {  // ← Исправлено!
            return $this->friend;
        }

        if ($this->friend_id === $currentUser->id) {  // ← Исправлено!
            return $this->user;
        }

        return null;
    }

    // === Проверки статусов ===

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isAccepted(): bool
    {
        return $this->status === self::STATUS_ACCEPTED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function isBlocked(): bool
    {
        return $this->status === self::STATUS_BLOCKED;
    }

    // === Scope-методы (для фильтрации в БД) ===

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeAccepted(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACCEPTED);
    }

    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('forest_user_id', $userId)  // ← Исправлено!
                     ->orWhere('friend_id', $userId);     // ← Исправлено!
    }
}