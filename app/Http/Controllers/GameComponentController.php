<?php

namespace App\Http\Controllers;

use App\Models\Dice;
use App\Models\Game;
use App\Models\GameComponent;
use Illuminate\Http\Request;

class GameComponentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Game $game)
    {
        return $game->components;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
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
     * @param  \App\Models\GameComponent  $gameComponent
     * @return \Illuminate\Http\Response
     */
    public function show(Game $game, GameComponent $gameComponent)
    {
        if ($gameComponent->game->id !== $game->id) {
            abort(404, 'Not found');
            return null;
        }
        return $gameComponent;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\GameComponent  $gameComponent
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Game $game, GameComponent $gameComponent)
    {
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
    }

    /**
     * Remove the specified resource from storage.
     *
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
    }
}
