<?php

namespace Database\Factories;

use App\Models\Subscription;
use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriptionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Subscription::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'plan_id' => rand(1, 2),
            'start_date' => date('Y-m-d H:i:s', time()),
            'end_date' => date('Y-m-d H:i:s', time() + 3600),
        ];
    }
}
