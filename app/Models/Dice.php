<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dice extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function roll() {
        return $this->gameComponent->setRanomOrientation();
    }
    public function edit($editData) {
        $updatedValues = [];
        
        // handle events (action)
        if (key_exists('event', $editData) && $editData['event'] == 'action') {
            $updatedValues['orientation'] = $this->roll();
        }

        return $updatedValues;
    }

    public function gameComponent()
    {
        return $this->morphOne(GameComponent::class, 'game_componentable');
    }
}
