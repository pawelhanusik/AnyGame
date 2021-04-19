<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'password', 'hand_dices', 'hand_cards'];
    protected $hidden = ['password'];

    public function addInitialComponents($dices = 0, $cards = 0) {
        $gameComponentsParams = [
            'pos_x' => 100,
            'pos_y' => 100,
        ];

        // add dices
        $gameComponentsParams['orientation'] = 0;
        for ($i = 0; $i < $dices; ++$i) {
            GameComponent::factory($gameComponentsParams)
                ->for($this)
                ->for(Dice::factory(), 'gameComponentable')
                ->create();
            $gameComponentsParams['pos_x'] += 10;
            if ($gameComponentsParams['pos_x'] > 500) {
                $gameComponentsParams['pos_x'] = 100;
                $gameComponentsParams['pos_y'] += 20;
            }
        }

        // add cards
        $gameComponentsParams['orientation'] = 5;
        for ($i = 0; $i < $cards; ++$i) {
            GameComponent::factory($gameComponentsParams)
                ->for($this)
                ->for(Card::factory(), 'gameComponentable')
                ->create();
            $gameComponentsParams['pos_x'] += 10;
            if ($gameComponentsParams['pos_x'] > 500) {
                $gameComponentsParams['pos_x'] = 100;
                $gameComponentsParams['pos_y'] += 20;
            }
        }
    }
    public function addComponentsForNewPlayer(Player $player) {
        $game = $player->game;
        $gameComponentsParams = [
            'owner_id' => $player->id,
            'editor_id' => $player->id,
        ];

        // add dices
        $gameComponentsParams['orientation'] = 0;
        for ($i = 0; $i < $game->hand_dices; ++$i) {
            GameComponent::factory($gameComponentsParams)
                ->for($game)
                ->for(Dice::factory(), 'gameComponentable')
                ->create();
        }

        // add cards
        $gameComponentsParams['orientation'] = 5;
        for ($i = 0; $i < $game->hand_cards; ++$i) {
            GameComponent::factory($gameComponentsParams)
                ->for($game)
                ->for(Card::factory(), 'gameComponentable')
                ->create();
        }
    }

    public function path($withPassword = false) {
        if ($withPassword) {
            return route('game.show', [$this, 'p' => $this->password], false);
        }
        return route('game.show', [$this], false);
    }

    public function isPublic() {
        return $this->password === NULL;
    }

    public function players() {
        return $this->hasMany(Player::class);
    }

    public function components() {
        return $this->hasMany(GameComponent::class);
    }
}
