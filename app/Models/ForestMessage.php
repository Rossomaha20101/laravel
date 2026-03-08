<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ForestMessage extends Model
{
    protected $fillable = ['sender_id', 'content'];
    
    public function sender(): BelongsTo
    {
        return $this->belongsTo(ForestUser::class, 'sender_id');
    }
}