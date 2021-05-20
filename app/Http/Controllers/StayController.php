<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Stay;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StayController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
    }

    public function lasts()
    {
        $stays = Stay::where('user_id', Auth::user()->id)->get();
        $nStays = [];
        foreach ($stays as $stay) {
            $nStay['id'] = $stay->id;
            $nStay['status'] = $stay->status;
            $nStay['date'] = $stay->created_at;
            $nStay['vehicle'] = Vehicle::where('id', $stay->vehicle_id)->first();
            $nStay['invoice'] = Invoice::where('id', $stay->invoice_id)->first();
            $nStays[] = $nStay;
        }

        return array_reverse($nStays);
    }

    public function insert(Request $request)
    {
        $vehicle = new Vehicle();
        $this->validate($request, [
            'plate' => 'required|min:7|max:7',
            'name' => 'required',
        ]);
        $vehicle->fill($request->all());
        $vehicle->user_id = Auth::user()->id;
        $vehicle->save();

        return $vehicle;
    }

    public function remove(Request $request)
    {
        $this->validate($request, [
            'plate' => 'required',
        ]);
        $vehicle = Vehicle::where('plate', $request->plate)->findOrFail();
        $vehicle->delete();

        return ['ok' => true];
    }
}
