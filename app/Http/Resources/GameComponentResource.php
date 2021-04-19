<?php

namespace App\Http\Resources;

use App\Models\Card;
use App\Models\Dice;
use Illuminate\Http\Resources\Json\JsonResource;

class GameComponentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'game_id' => $this->game_id,
            'component_type' => $this->game_componentable_type,
            'component' => $this->when(true, function() {
                switch(true) {
                    case $this->gameComponentable instanceof Dice:
                        return new DiceResource($this->gameComponentable);
                    case $this->gameComponentable instanceof Card:
                        return new CardResource($this->gameComponentable);
                    default:
                        return null;
                }
            }),
            
            'posX' => $this->pos_x,
            'posY' => $this->pos_y,
            'orientation' => $this->orientation,
            
            'is_owner' => ($this->owner_id == auth()->guard('player')->id()),
            'is_editor' => ($this->editor_id == auth()->guard('player')->id()),
            'has_editor' => ($this->editor_id !== null),
            'visibility' => (($this->owner_id !== null && $this->owner_id != auth()->guard('player')->id()) ? 'hidden' : 'visible'),
        ];
    }
}
