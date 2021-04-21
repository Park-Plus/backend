<?php

namespace Database\Seeders;

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
        $vehicles = new Collection([]);
        foreach($users as $user){
            $vehicles = $vehicles->merge(Vehicle::factory(["user_id" => $user->id])->count(1)->create());
        }
    }
}
