<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\Place;
use App\Models\Stay;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $me = User::factory(["name" => "Mario", "surname" => "Rossi", "cf" => "RSSMRA80A01H501U", "email" => "mario@rossi.it", "password" => Hash::make("password")])->create();
        $veh = Vehicle::factory(["user_id" => $me])->create();
        $inv = Invoice::factory(["user_id" => $me])->create();
        Stay::factory(["user_id" => $me, "vehicle_id" => $veh, "status" => "ended", "invoice_id" => $inv])->create();
        $users = User::factory()->count(10)->create();
        $vehicles = new Collection([]);
        foreach($users as $user){
            $avStates = ["active", "ended"];
            $vehicles = Vehicle::factory(["user_id" => $user->id])->count(rand(1, 5))->create();
            for($i = 0; $i < 5; $i++){
                $ext = $avStates[array_rand($avStates)];
                if($ext == "active"){
                    if(in_array("active", $avStates)){
                        $stay = Stay::factory(["user_id" => $user->id, "vehicle_id" => $vehicles[array_rand($vehicles->toArray())]->id, "status" => "active"])->create();
                        $index = array_search('active', $avStates);
                        if($index !== FALSE){
                            unset($avStates[$index]);
                        }
                    }else{
                        $inv = Invoice::factory(["user_id" => $user->id])->create();
                        $stay = Stay::factory(["user_id" => $user->id, "vehicle_id" => $vehicles[array_rand($vehicles->toArray())]->id, "status" => "ended", "invoice_id" => $inv->id])->create(); 
                    }
                }else{
                    $inv = Invoice::factory(["user_id" => $user->id])->create();
                    $stay = Stay::factory(["user_id" => $user->id, "vehicle_id" => $vehicles[array_rand($vehicles->toArray())]->id, "status" => "ended", "invoice_id" => $inv->id])->create();
                }
                echo var_dump($avStates);
            }
        }
        for($i = 1; $i < 11; $i++){
            Place::factory(["name" => "A".$i])->create();
            Place::factory(["name" => "B".$i])->create();
        }
    }
}
