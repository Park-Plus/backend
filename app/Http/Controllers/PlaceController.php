<?php

namespace App\Http\Controllers;

use App\Models\Place;
use Illuminate\Support\Facades\DB;

class PlaceController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
    }

    public function status()
    {
        $places = DB::table('places')->orderBy('section')->orderBy('number')->get();
        $freePlaces = DB::table('places')->where('status', 'free')->get()->count();

        return ['free' => $freePlaces, 'list' => $places];
    }

    public function getFree()
    {
        return Place::where('status', 'free')->first();
    }
}
