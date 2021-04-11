<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameComponent extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function setOrientation($orientation) {
        if ($orientation < 0 || $orientation > 5) {
            return;
        }
        $this->orientation = $orientation;
        $this->save();
        
        return $this->orientation;
    }
    public function setRanomOrientation() {
        $this->orientation = random_int(0, 5);
        $this->save();

        return $this->orientation;
    }
    public function edit($editData) {
        $updatedValues = [];

        // update gameComponent
        if (key_exists('posX', $editData)) {
            $updatedValues['posX'] = $editData['posX'];
            $this->pos_x = $editData['posX'];
        }
        if (key_exists('posY', $editData)) {
            $updatedValues['posY'] = $editData['posY'];
            $this->pos_y = $editData['posY'];
        }
        $this->save($updatedValues);
        
        // update gameComponentable
        if (method_exists($this->gameComponentable, 'edit')) {
            $updatedValues = array_merge($updatedValues, $this->gameComponentable->edit($editData));
        }
        
        return $updatedValues;
    }

    // relations

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
