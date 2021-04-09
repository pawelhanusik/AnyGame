<?php

namespace Database\Factories;

use App\Models\GameComponent;
use Illuminate\Database\Eloquent\Factories\Factory;

class GameComponentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = GameComponent::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'pos_x' => random_int(100, 1000),
            'pos_y' => random_int(100, 500),
            'rot_x' => 0,
            'rot_y' => 0,
            'rot_z' => 0
        ];
    }
}
