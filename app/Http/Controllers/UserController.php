<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\User;
use Cartalyst\Stripe\Laravel\Facades\Stripe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
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

    public function me(){
        return Auth::user();
    }

    public function vehicles(){
        $vehicles = User::find(Auth::user()->id)->first()->vehicles()->get(); //TODO: escludere i campi degli update e fixare sta roba
        return $vehicles;
    }

    public function invoices(){
        $vehicles = User::find(Auth::user()->id)->first()->invoices()->get(); //TODO: escludere i campi degli update e fixare sta roba
        return $vehicles;
    }

    public function edit(){
        // TODO
    }

    //
}
