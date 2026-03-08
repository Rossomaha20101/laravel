<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AnimalType extends Model
{
    protected $fillable = ['name'];
    
    public function forestUsers(): HasMany
    {
        return $this->hasMany(ForestUser::class);
    }
}