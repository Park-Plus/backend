<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use PhpParser\Node\Expr\Cast\String_;

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
        return [
            'name' => $this->faker->firstName,
            'surname' => $this->faker->lastName,
            'cf' => $this->faker->taxId(),
            'email' => $this->faker->unique()->safeEmail,
            'profile_picture' => "https://avatars.githubusercontent.com/u/".rand(1, 100000),
            'password' => Hash::make('password')
        ];
    }
}
