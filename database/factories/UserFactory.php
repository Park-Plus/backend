<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $this->faker->addProvider(new \Faker\Provider\it_IT\Person($this->faker));
        $plans = ['free', 'premium'];

        $name = $this->faker->firstName;
        $surname = $this->faker->lastName;

        return [
            'name' => $name,
            'surname' => $surname,
            'cf' => $this->faker->taxId(),
            'email' => substr($name, 0, 3) . '.' . $surname . "@example.it",
            'profile_picture' => 'https://avatars.githubusercontent.com/u/' . rand(1, 100000),
            'password' => Hash::make('password'),
            'plan' => $plans[array_rand($plans)],
        ];
    }
}
