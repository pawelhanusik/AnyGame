<?php

namespace Tests\Feature;

use App\Models\Card;
use App\Models\Game;
use App\Models\GameComponent;
use App\Models\Player;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class GameComponentTest extends TestCase
{
    private $game;
    private $component;
    private $player;

    protected function setUp() :void {
        parent::setUp();

        $this->post('/create', [
            'name' => "aaa",
            'hand_dices' => 1,
            'hand_cards' => 2,
            'table_dices' => 3,
            'table_cards' => 4,
            'public' => true,
        ])->assertOk();
        $this->game = Game::where('name', "aaa")->get()->last();

        $this->component = GameComponent::factory()
            ->for($this->game)
            ->for(Card::factory(), 'gameComponentable')
            ->create()
        ;

        $this->player = Player::create([
            'nick' => 'test_player',
            'game_id' => $this->game->id,
        ]);
    }

    public function testIndex() {
        $this->get('/' . $this->game->id . '/components')
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->has('data', 8)
            )
        ;

        $this->get('/' . $this->game->id . '?nick=player1')
            ->assertOk()
        ;
        $this->get('/' . $this->game->id . '?nick=player2')
            ->assertOk()
        ;
        
        $this->get('/' . $this->game->id . '/components')
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->has('data', 14)
            )
        ;
    }
    
    public function testShow() {
        $this->post('/create', [
            'name' => "aaa",
            'hand_dices' => 1,
            'hand_cards' => 2,
            'table_dices' => 3,
            'table_cards' => 4,
            'public' => true,
        ])->assertOk();
        
        $components = GameComponent::where('game_id', $this->game->id)->get();

        foreach ($components as $c) {
            $this->get('/' . $this->game->id . '/components/' . $c->id)
                ->assertOk()
                ->assertJson(fn (AssertableJson $json) => $json
                    ->has('data')
                    ->where('data.id', $c->id)
                    ->where('data.game_id', $c->game_id)
                    ->where('data.component_type', $c->game_componentable_type)
                )
            ;
        }
    }

    public function testEditRights() {
        $this->actingAs($this->player)->get('/' . $this->game->id . '/components/' . $this->component->id . '/editrights')
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('granted', true)
            )
        ;
        $this->actingAs($this->player)->delete('/' . $this->game->id . '/components/' . $this->component->id . '/editrights')
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('abandoned', true)
            )
        ;
    }

    public function testOwnership() {
        $this->actingAs($this->player)->get('/' . $this->game->id . '/components/' . $this->component->id . '/editrights')
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('granted', true)
            )
        ;
        $this->actingAs($this->player)->get('/' . $this->game->id . '/components/' . $this->component->id . '/ownership')
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('granted', true)
            )
        ;
        $this->actingAs($this->player)->delete('/' . $this->game->id . '/components/' . $this->component->id . '/ownership')
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('abandoned', true)
            )
        ;
        $this->actingAs($this->player)->delete('/' . $this->game->id . '/components/' . $this->component->id . '/editrights')
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('abandoned', true)
            )
        ;
    }

    public function testUpdate() {
        $this->put('/' . $this->game->id . '/components/' . $this->component->id, [
            'posX' => 2,
            'posY' => 3,
            'event' => 'action',
        ])
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('posX', 2)
                ->where('posY', 3)
                ->has('orientation')
            )
        ;
    }
}
