<?php

namespace App\Http\Controllers;

use App\Events\GameComponentUpdateEvent;
use App\Http\Resources\GameComponentResource;
use App\Models\Dice;
use App\Models\Game;
use App\Models\GameComponent;
use Illuminate\Http\Request;

class GameComponentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \App\Models\Game  $game
     * @return \Illuminate\Http\Response
     */
    public function index(Game $game)
    {
        return GameComponentResource::collection($game->components);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Game  $game
     * @param  \App\Models\GameComponent  $gameComponent
     * @return \Illuminate\Http\Response
     */
    public function show(Game $game, GameComponent $gameComponent)
    {
        if ($gameComponent->game->id !== $game->id) {
            abort(404, 'Not found');
            return null;
        }
        return new GameComponentResource($gameComponent);
    }


    /**
     * Grand the player rights to edit the gameComponent
     *
     * @param  \App\Models\Game  $game
     * @param  \App\Models\GameComponent  $gameComponent
     * @return \Illuminate\Http\Response
     */
    public function grantEditRights(Game $game, GameComponent $gameComponent)
    {
        if ($gameComponent->owner !== null) {
            // component is not on table
            return ['granted' => false];
        }
        if ($gameComponent->editor !== null) {
            return ['granted' => false];
        }
        
        $gameComponent->editor_id = auth()->guard('player')->id();
        $gameComponent->save();

        broadcast(new GameComponentUpdateEvent($game, $gameComponent, ['hasEditor' => true]))->toOthers();

        return ['granted' => true];
    }
    /**
     * Remove edit rights of the player
     *
     * @param  \App\Models\Game  $game
     * @param  \App\Models\GameComponent  $gameComponent
     * @return \Illuminate\Http\Response
     */
    public function abandonEditRights(Game $game, GameComponent $gameComponent)
    {
        if ($gameComponent->editor?->id !== auth()->guard('player')->id()) {
            return ['abandoned' => false];
        }
        $gameComponent->editor_id = null;
        $gameComponent->save();

        broadcast(new GameComponentUpdateEvent($game, $gameComponent, ['hasEditor' => false]))->toOthers();

        return ['abandoned' => true];
    }
    /**
     * Grand the player ownership of the gameComponent
     *
     * @param  \App\Models\Game  $game
     * @param  \App\Models\GameComponent  $gameComponent
     * @return \Illuminate\Http\Response
     */
    public function grantOwnership(Game $game, GameComponent $gameComponent)
    {
        if ($gameComponent->owner !== null) {
            // component is not on table
            return ['granted' => false, 'error' => 'owner is ' . $gameComponent->owner->nick];
        }
        if ($gameComponent->editor?->id !== auth()->guard('player')->id()) {
            // to own the component, one have edit it
            return ['granted' => false, 'error' => 'you are not an editor'];
        }
        
        $gameComponent->owner_id = auth()->guard('player')->id();
        $gameComponent->save();

        broadcast(new GameComponentUpdateEvent($game, $gameComponent, ['visibility' => 'hidden']))->toOthers();

        return ['granted' => true];
    }
    /**
     * Remove ownership of the player
     *
     * @param  \App\Models\Game  $game
     * @param  \App\Models\GameComponent  $gameComponent
     * @return \Illuminate\Http\Response
     */
    public function abandonOwnership(Game $game, GameComponent $gameComponent)
    {
        if ($gameComponent->owner?->id !== auth()->guard('player')->id()) {
            return ['abandoned' => false];
        }
        $gameComponent->owner_id = null;
        $gameComponent->save();

        broadcast(new GameComponentUpdateEvent($game, $gameComponent, ['visibility' => 'visible']))->toOthers();

        return ['abandoned' => true];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Game  $game
     * @param  \App\Models\GameComponent  $gameComponent
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Game $game, GameComponent $gameComponent)
    {
        if ($gameComponent->owner !== null && $gameComponent->owner->id !== auth()->guard('player')->id()) {
            // component is not on table & the player is not the owner - can't update
            abort(404, 'Not found');
            return null;
        }
        if ($gameComponent->editor?->id !== auth()->guard('player')->id()) {
            // user hasn't been granted permissions to update the component
            abort(403);
            return null;
        }
        if ($gameComponent->game->id !== $game->id) {
            abort(404, 'Not found');
            return null;
        }

        $updatedValues = $gameComponent->edit($request->all());
        broadcast(new GameComponentUpdateEvent($game, $gameComponent, $updatedValues))->toOthers();

        return $updatedValues;
    }
}
