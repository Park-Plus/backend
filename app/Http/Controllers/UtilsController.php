<?php

namespace App\Http\Controllers;

use App\Models\User;
use Cartalyst\Stripe\Laravel\Facades\Stripe;

class UtilsController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
    }

    public function empty()
    {
        $users = User::all();
        foreach ($users as $user) {
            Stripe::customers()->delete($user->stripe_user_id);
        }

        return [];
    }
}
