<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\Place;
use App\Models\Stay;
use App\Models\User;
use App\Models\Vehicle;
use Cartalyst\Stripe\Laravel\Facades\Stripe;
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
        $creditCards = ['4242424242424242', '4000056655665556', '5555555555554444', '5200828282828210', '5105105105105100'];
        $me = User::factory(["name" => "Mario", "surname" => "Rossi", "cf" => "RSSMRA80A01H501U", "email" => "mario@rossi.it", "password" => Hash::make("password")])->create();
        $customer = Stripe::customers()->create([
            'name' => $me->name . " " . $me->surname,
            'email' => $me->email,
        ]);
        $me->stripe_user_id = $customer['id'];
        $me->save();
        $tok = Stripe::tokens()->create([
            'card' => [
                'number'    => $creditCards[array_rand($creditCards)],
                'exp_month' => rand(6, 12),
                'cvc'       => rand(100, 999),
                'exp_year'  => 2021,
            ]
        ]);
        Stripe::cards()->create($customer['id'], $tok['id']);
        $veh = Vehicle::factory(["user_id" => $me])->create();
        $inv = Invoice::factory(["user_id" => $me])->create();
        Stay::factory(["user_id" => $me, "vehicle_id" => $veh, "status" => "ended", "invoice_id" => $inv])->create();
        $veh = Vehicle::factory(["user_id" => $me])->create();
        $inv = Invoice::factory(["user_id" => $me])->create();
        Stay::factory(["user_id" => $me, "vehicle_id" => $veh, "status" => "ended", "invoice_id" => $inv])->create();
        $users = User::factory()->count(10)->create();
        $vehicles = new Collection([]);
        foreach($users as $user){
            $customer = Stripe::customers()->create([
                'name' => $user->name . " " . $user->surname,
                'email' => $user->email,
            ]);
            $user->stripe_user_id = $customer['id'];
            $user->save();
            $tok = Stripe::tokens()->create([
                'card' => [
                    'number'    => $creditCards[array_rand($creditCards)],
                    'exp_month' => rand(6, 12),
                    'cvc'       => rand(100, 999),
                    'exp_year'  => 2021,
                ]
            ]);
            Stripe::cards()->create($customer['id'], $tok['id']);
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
            }
        }
        for($i = 1; $i < 11; $i++){
            Place::factory(["section" => "A", "number" => $i])->create();
            Place::factory(["section" => "B", "number" => $i])->create();
        }
    }
}
