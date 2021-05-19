<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Stay;
use App\Models\User;
use App\Models\Vehicle;
use Cartalyst\Stripe\Laravel\Facades\Stripe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UtilsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function empty(){
        $users = User::all();
        foreach($users as $user){
            Stripe::customers()->delete($user->stripe_user_id);
        }
        return [];
    }
}
