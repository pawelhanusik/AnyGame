<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    use HasFactory;

    protected $fillable = ['nick', 'game_id', 'ip'];
    protected $hidden = ['ip', 'created_at', 'updated_at'];

    public function game() {
        return $this->belongsTo(Game::class);
    }
}
