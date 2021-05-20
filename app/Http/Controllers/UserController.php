<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
    }

    public function me()
    {
        return Auth::user();
    }

    public function vehicles()
    {
        return User::find(Auth::user()->id)->first()->vehicles()->get(); //TODO: escludere i campi degli update e fixare sta roba
    }

    public function invoices()
    {
        return User::find(Auth::user()->id)->first()->invoices()->get(); //TODO: escludere i campi degli update e fixare sta roba
    }

    public function edit()
    {
        // TODO
    }
}
