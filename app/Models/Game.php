<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'password'];
    protected $hidden = ['password'];

    public function path($withPassword = false) {
        if ($withPassword) {
            return route('game.show', [$this, 'p' => $this->password], false);
        }
        return route('game.show', [$this], false);
    }

    public function isPublic() {
        return $this->password === NULL;
    }
}
