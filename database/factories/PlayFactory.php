<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PlayFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $dice1 = random_int(1,6);
        $dice2 = random_int(1,6);
        $sum = $dice1 + $dice2;
        $result = ($sum == 7) ? "you win! :)" : "you lost... :(";
        return [
            'user_id' => $this->faker->randomElement(['1', '2', '3', '4', '5', '6']),
            'dice1' => $dice1,
            'dice2' => $dice2,
            'sum' => $sum,
            'result' => $result,
        ];
    }
}
