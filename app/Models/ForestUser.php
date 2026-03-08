<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Sanctum\HasApiTokens;

class ForestUser extends Authenticatable
{
    //use Notifiable;
    use HasApiTokens, Notifiable;

    //protected $table = 'forest_users';

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
     * Связь: Тип животного (многие-к-одному)
     */
    public function animalType(): BelongsTo
    {
        return $this->belongsTo(AnimalType::class);
    }

    /**
     * Связь: Друзья пользователя (многие-ко-многим)
     * Предполагается таблица-связка: forest_user_friends
     */
    public function friends(): BelongsToMany
    {
        return $this->belongsToMany(
            ForestUser::class,
            'forest_friendships',
            'user_id',
            'friend_id'
        )->withTimestamps();
    }

    public function sentFriendRequests(): HasMany
    {
        return $this->hasMany(ForestFriendship::class, 'sender_id');
    }

    public function receivedFriendRequests(): HasMany
    {
        return $this->hasMany(ForestFriendship::class, 'receiver_id');
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(ForestMessage::class, 'sender_id');
    }

    public function receivedMessages(): HasMany
    {
        return $this->hasMany(ForestMessage::class, 'receiver_id');
    }

    public function recommendations(): HasMany
    {
        return $this->hasMany(Recommendation::class);
    }
}