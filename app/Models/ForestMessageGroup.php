<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ForestMessageGroup extends Model
{
    protected $fillable = ['name', 'created_by'];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(ForestUser::class, 'created_by');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(ForestUser::class, 'forest_message_group_users', 'group_id', 'user_id')
                    ->withTimestamps();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ForestMessage::class, 'group_id');
    }

    public function isMember(ForestUser $user): bool
    {
        return $this->users()->where('forest_users.id', $user->id)->exists();
    }

    public function addMember(ForestUser $user): void
    {
        if (!$this->isMember($user)) {
            $this->users()->attach($user->id);
        }
    }
}