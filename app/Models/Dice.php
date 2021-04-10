<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dice extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function gameComponent()
    {
        return $this->morphOne(GameComponent::class, 'game_componentable');
    }
}
