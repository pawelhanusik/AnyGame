<?php

namespace Database\Seeders;

use App\Models\Dice;
use App\Models\Game;
use App\Models\GameComponent;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $game = Game::factory()->create();

        for ($i = 0; $i < 10; ++$i) {
            GameComponent::factory()
                ->for($game)
                ->for(Dice::factory(), 'gameComponentable')
                ->create();
        }
    }
}
