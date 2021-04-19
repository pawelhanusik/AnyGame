<?php

namespace App\Providers;

use App\Events\PlayerJoinEvent;
use App\Models\Card;
use App\Models\Dice;
use App\Models\GameComponent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AddGameComponentsOnPlayerJoin
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  PlayerJoinEvent  $event
     * @return void
     */
    public function handle(PlayerJoinEvent $event)
    {
        $player = $event->player;
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
}
