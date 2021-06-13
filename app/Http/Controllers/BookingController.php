<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Place;
use App\Models\User;
use Cartalyst\Stripe\Laravel\Facades\Stripe;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {

    }

    public function available(Request $request){
        $start = $request->input("start");
        $end = $request->input("end"); // TODO: Validazione richieste
        $freePlaces = Place::where('status', 'free')->get();
        $availablePlaces = [];
        foreach($freePlaces as $place){
            $alreadyExistingBookings = Booking::where(function($query) use ($start, $end){
                $query->whereBetween('start', [$start, $end])
                ->orWhereBetween('start', [$start, $end]);
            })->get();
            if(count($alreadyExistingBookings) == 0){
                $availablePlaces[] = $place;
            }
        }
        return $availablePlaces;
    }
}
