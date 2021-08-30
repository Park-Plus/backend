<?php

namespace App\Http\Controllers;

use App\Models\Place;
use Illuminate\Http\Request;
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

    public function setStatus(Request $request)
    {
        $status = $request->input('status');
        $parkID = $request->input('park_id');
        $pl = Place::where(['section' => str_split($parkID)[0], 'number' => str_split($parkID)[1]])->firstOrFail();
        $pl->status = $status;
        $pl->save();

        return $pl;
    }
}
