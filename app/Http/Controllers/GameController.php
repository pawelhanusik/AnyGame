<?php

namespace App\Http\Controllers;

use App\Events\PlayerJoinEvent;
use App\Events\TestEvent;
use App\Models\Game;
use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return inertia('Game/Index');
    }

    public function create() {
        return inertia('Game/Create', [
            'componentTyoes' => [
                'dices',
                'cards'
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|max:50',
            'hand_dices' => 'integer',
            'hand_cards' => 'integer'
        ]);

        $password = "";
        if ($request->has('public') && $request['public'] === true) {
            $password = NULL;
        }
        if ($password === "") {
            $password = Str::random(8);
        }
        $data['password'] = $password;

        $game = Game::create($data);
        $game->addInitialComponents(
            $request['table_dices'] ?? 0,
            $request['table_cards'] ?? 0
        );
        return inertia('Game/Store', [
            'name' => $game->name,
            'url' => $game->path(true),
            'password' => $game->password
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Game  $game
     * @return \Illuminate\Http\Response
     */
    public function show(Game $game)
    {
        $password = "";
        if (request()->has('p')) {
            $password = request('p');
        }

        if (!request()->has('nick') || strlen(request('nick')) <= 0 ) {
            return inertia('Game/Join', [
                'game' => $game,
                'isPublic' => $game->isPublic(),
                'p' => $password
            ]);
        }
        
        $nick = request('nick');
        $player = Player::where('game_id', $game->id)->where('nick', $nick)->first();
        $playerExists = ($player !== null);
        $isUserAuthenticated = ($playerExists && auth()->guard('player')->id() === $player->id);
        if ($playerExists && !$isUserAuthenticated) {
            return inertia('Game/Join', [
                'game' => $game,
                'isPublic' => $game->isPublic(),
                'nick' => $nick,
                'p' => $password,
                'error' => 'Nick is already taken'
            ]);
        }

        if ($password !== $game->password) {
            return inertia('Game/Join', [
                'game' => $game,
                'isPublic' => $game->isPublic(),
                'nick' => $nick,
                'p' => '',
                'error' => 'Bad password'
            ]);
        }
        
        // TODO: Simplify all of these if's
        if (!$playerExists) {
            $player = Player::create([
                'nick' => $nick,
                'game_id' => $game->id
            ]);
            
            /** @var \Illuminate\Contracts\Auth\SessionGuard */
            $playerAuthGuard = auth()->guard('player');
            $playerAuthGuard->login($player);
        } else if ($isUserAuthenticated){
            $player->touch();
        } else {
            return;
        }
        
        PlayerJoinEvent::dispatch($player);

        return inertia('Game/Show', [
            'nick' => $nick,
            'game' => $game,
            'players' => Player::where('game_id', $game->id)->get()
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Game  $game
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Game $game)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Game  $game
     * @return \Illuminate\Http\Response
     */
    public function destroy(Game $game)
    {
        //
    }
}
