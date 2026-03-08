<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class ForestFriendship extends Model
{
    protected $table = 'forest_friendships';

    protected $fillable = [
        'sender_id',
        'receiver_id',
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
     * Связь: Отправитель заявки
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(ForestUser::class, 'sender_id');
    }

    /**
     * Связь: Получатель заявки
     */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(ForestUser::class, 'receiver_id');
    }

    /**
     * Получить пользователя, противоположного данному
     * (удобно для отображения в списке друзей)
     */
    public function getOtherUser(ForestUser $user): ?ForestUser
    {
        if ($this->sender_id === $user->id) {
            return $this->receiver;
        }

        if ($this->receiver_id === $user->id) {
            return $this->sender;
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
        return $query->where('sender_id', $userId)
                     ->orWhere('receiver_id', $userId);
    }
}