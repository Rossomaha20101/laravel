<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ForestMessage extends Model
{
    protected $fillable = [
        'sender_id',
        'recipient_id',
        'group_id',
        'type',
        'content',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(ForestUser::class, 'sender_id');
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(ForestUser::class, 'recipient_id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(ForestMessageGroup::class, 'group_id');
    }

    public function isPersonal(): bool
    {
        return $this->type === 'personal';
    }

    public function isGroup(): bool
    {
        return $this->type === 'group';
    }
}