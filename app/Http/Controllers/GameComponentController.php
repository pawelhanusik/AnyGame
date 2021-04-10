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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Game  $game
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Game $game)
    {
        $validated = $request->validate([
            'type' => 'required|string',

            'posX' => 'required|integer',
            'posY' => 'required|integer',
            'rotX' => 'required|integer',
            'rotY' => 'required|integer',
            'rotZ' => 'required|integer'
        ]);

        $component = new GameComponent();
        $component->game_id = $game->id;
        
        $component->pos_x = $validated['posX'];
        $component->pos_y = $validated['posY'];
        $component->rot_x = $validated['rotX'];
        $component->rot_y = $validated['rotY'];
        $component->rot_z = $validated['rotZ'];
        
        $componentable = null;
        if (strtolower($validated['type']) == 'dice') {
            $componentable = Dice::create();
        }

        if ($componentable === null) {
            abort(422);
            return null;
        }

        $component->gameComponentable()->associate($componentable);
        $component->save();
        
        return null;
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
        if ($gameComponent->owner !== null) {
            // component is not on table - can't update
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

        $updatedValues = [];

        if ($request->has('posX')) {
            $updatedValues['pos_x'] = $request['posX'];
        }
        if ($request->has('posY')) {
            $updatedValues['pos_y'] = $request['posY'];
        }
        if ($request->has('rotX')) {
            $updatedValues['rot_x'] = $request['rotX'];
        }
        if ($request->has('rotY')) {
            $updatedValues['rot_y'] = $request['rotY'];
        }
        if ($request->has('rotZ')) {
            $updatedValues['rot_z'] = $request['rotZ'];
        }

        if ($request->has('type')) {
            if (strtolower($request['type']) == "dice") {
                // dices don't have any properties
            }
        }

        $gameComponent->update($updatedValues);
        broadcast(new GameComponentUpdateEvent($game, $gameComponent, $updatedValues))->toOthers();

        return null;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Game  $game
     * @param  \App\Models\GameComponent  $gameComponent
     * @return \Illuminate\Http\Response
     */
    public function destroy(Game $game, GameComponent $gameComponent)
    {
        if ($gameComponent->game->id !== $game->id) {
            abort(404, 'Not found');
            return null;
        }

        $gameComponent->gameComponentable()->delete();
        $gameComponent->delete();

        return null;
    }
}
