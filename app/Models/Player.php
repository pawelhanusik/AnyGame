<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Notifications\Notifiable;

class Player extends Model implements AuthenticatableContract
{
    use HasFactory, Notifiable, Authenticatable;

    protected $fillable = [
        'nick',
        'game_id'
    ];
    protected $hidden = [
        'remember_token',
        'created_at',
        'updated_at'
    ];

    public function game() {
        return $this->belongsTo(Game::class);
    }
}
