<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function flip() {
        $orientation = $this->gameComponent->orientation;
        $newOrientation = null;
        if ($orientation == 0) {
            $newOrientation = 5;
        } else {
            $newOrientation = 0;
        }

        if ($newOrientation !== null) {
            $this->gameComponent->setOrientation($newOrientation);
            return $newOrientation;
        }
        return $orientation;
    }
    public function edit($editData) {
        $updatedValues = [];
        
        // handle events (action)
        if (key_exists('event', $editData) && $editData['event'] == 'action') {
            $updatedValues['orientation'] = $this->flip();
        }

        return $updatedValues;
    }

    public function gameComponent()
    {
        return $this->morphOne(GameComponent::class, 'game_componentable');
    }
}
