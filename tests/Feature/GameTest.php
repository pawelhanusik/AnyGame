<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\Player;
use Inertia\Testing\Assert;
use Tests\TestCase;

class GameTest extends TestCase
{
    public function testIndex() {
        $response = $this->get('/');
        $response->assertOk();
    }

    public function testCreateGame() {
        for ($i = 0; $i < 2; ++$i) {
            $data = [
                'name' => "Test new game",
                'hand_dices' => 1,
                'hand_cards' => 2,
                'table_dices' => 1,
                'table_cards' => 2,
                'public' => ($i % 2 === 0),
            ];

            $response = $this->post('/create', $data);
            $response->assertOk();
            $response->assertInertia(fn (Assert $page) => $page
                ->component('Game/Store')
                ->where('name', $data['name'])
                ->where('password', function ($value) use ($data) {
                    if ($data['public'] === true) {
                        return strlen($value) === 0;
                    } else {
                        return strlen($value) > 0;
                    }
                })
                ->has('url')
            );
        }
    }

    public function testGameJoin() {
        $game = Game::all()->random();
        $url = '/' . $game->id;

        $response = $this->get($url);
        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Game/Join')
            ->where('game.name', $game->name)
        );
    }
    
    public function testGameShow() {
        $game = null;
        if (Game::where('public', true)->count() > 0) {
            $game = Game::where('public', true)->get()->random();
        } else {
            $game = Game::factory(['password' => 'aaa'])->create();
        }

        $nick1 = "admin1";
        $nick2 = "admin2";
        while( Player::where('game_id', $game->id)->where('nick', $nick1)->count() > 0 ) {
            $nick1 .= '_';
        }
        while( Player::where('game_id', $game->id)->where('nick', $nick2)->count() > 0 ) {
            $nick2 .= '_';
        }

        $url1 = '/' . $game->id;
        $url1 .= '?nick=' . $nick1;
        $url1 .= '&p=' . $game->password;
        $url1_bad_passwd = $url1 . 'aaaaa';
        
        $url2 = '/' . $game->id;
        $url2 .= '?nick=' . $nick2;
        $url2 .= '&p=' . $game->password;

        // login as player1
        $response = $this->get($url1);
        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Game/Show')
            ->where('nick', $nick1)
            ->where('game.name', $game->name)
        );
        
        // try to rejoin as player1
        $response = $this->actingAs(
            Player::where('nick', $nick1)->where('game_id', $game->id)->get()->first()
        )->get($url1);
        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Game/Show')
            ->where('nick', $nick1)
            ->where('game.name', $game->name)
        );

        // try to rejoin with bad password
        $response = $this->actingAs(
            Player::where('nick', $nick1)->where('game_id', $game->id)->get()->first()
        )->get($url1_bad_passwd);
        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Game/Join')
            ->where('nick', $nick1)
            ->where('error', 'Bad password')
        );

        // login as player2
        $response = $this->get($url2);
        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Game/Show')
            ->where('nick', $nick2)
            ->where('game.name', $game->name)
        );

        // try to rejoin as player2
        $response = $this->actingAs(
            Player::where('nick', $nick2)->where('game_id', $game->id)->get()->first()
        )->get($url2);
        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Game/Show')
            ->where('nick', $nick2)
            ->where('game.name', $game->name)
        );

        // try to rejoin as player1 (should be rejected, since currently logged in user is player2)
        $response = $this->actingAs(
            Player::where('nick', $nick1)->where('game_id', $game->id)->get()->first()
        )->get($url1);
        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Game/Join')
            ->where('nick', $nick1)
            ->where('error', 'Nick is already taken')
        );
    }
}
