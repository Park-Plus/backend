<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::factory()->count(10)->create();
        Plan::factory(["name" => "Free", "price_monthly" => 0])->create();
        Plan::factory(["name" => "Premium", "price_monthly" => 9.99])->create();
        $vehicles = new Collection([]);
        foreach($users as $user){
            Subscription::factory(["user_id" => $user->id])->create();
            $vehicles = $vehicles->merge(Vehicle::factory(["user_id" => $user->id])->count(1)->create());
        }
    }
}
