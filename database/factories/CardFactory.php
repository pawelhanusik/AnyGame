<?php

namespace Database\Factories;

use App\Models\Card;
use Illuminate\Database\Eloquent\Factories\Factory;

class CardFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Card::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $suits = ['spades', 'hearths', 'diamonds', 'clubs'];
        $ranks = ['2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K', 'A'];
        return [
            'suit' => $suits[random_int(0, count($suits) - 1)],
            'rank' => $ranks[random_int(0, count($ranks) - 1)]
        ];
    }
}
