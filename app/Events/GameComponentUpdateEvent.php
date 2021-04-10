<?php

namespace App\Events;

use App\Models\Game;
use App\Models\GameComponent;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GameComponentUpdateEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $gameID;
    public $componentID;
    public $updatedValues;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Game $game, GameComponent $gameComponent, $updatedValues)
    {
        $this->gameID = $game->id;
        $this->componentID = $gameComponent->id;
        $this->updatedValues = $updatedValues;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PresenceChannel('game-channel.' . $this->gameID);
    }
}
