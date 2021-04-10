<?php

namespace App\Http\Controllers;

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
        return inertia('Game/Create');
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
            'name' => ['required', 'max:50']
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
        $tmpPlayer = Player::where('game_id', $game->id)->where('nick', $nick)->first();
        $playerExists = ($tmpPlayer !== null);
        $isUserAuthenticated = ($playerExists && auth()->guard('player')->id() === $tmpPlayer->id);
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
            $tmpPlayer->touch();
        } else {
            return;
        }
        
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
