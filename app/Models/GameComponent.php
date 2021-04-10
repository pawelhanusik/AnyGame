<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameComponent extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function gameComponentable()
    {
        return $this->morphTo();
    }

    public function game() {
        return $this->belongsTo(Game::class);
    }

    public function owner() {
        return $this->belongsTo(Player::class, 'owner_id');
    }
    public function editor() {
        return $this->belongsTo(Player::class, 'editor_id');
    }
}
