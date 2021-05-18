<?php

namespace Database\Factories;

use App\Models\Place;
use App\Models\Stay;
use Illuminate\Database\Eloquent\Factories\Factory;

class StayFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Stay::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $statuses = ["active", "ended"];
        return [
            'status' => $statuses[array_rand($statuses)]
        ];
    }
}
