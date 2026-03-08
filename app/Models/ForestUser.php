<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, BelongsToMany, HasMany};
use Laravel\Sanctum\HasApiTokens;

class ForestUser extends Authenticatable
{
    use HasApiTokens;

    protected $fillable = [
        'name', 'nickname', 'animal_type_id', 'gender', 'birth_date',
        'best_friend_name', 'email', 'password',
    ];

    protected $hidden = ['password', 'remember_token'];

    public function animalType(): BelongsTo
    {
        return $this->belongsTo(AnimalType::class);
    }

    public function friends(): BelongsToMany
    {
        return $this->belongsToMany(
            ForestUser::class, 
            'forest_friendships', 
            'forest_user_id', 
            'friend_id'
        );
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ForestMessage::class, 'sender_id');
    }
}