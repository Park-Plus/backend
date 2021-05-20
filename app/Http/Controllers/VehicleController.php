<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Stay;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class VehicleController extends Controller
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

    public function list(){
        /** @var App\Models\User $user */
        $user = Auth::user();

        $vehicles = $user->vehicles()->get();
        $nVeh = [];
        foreach($vehicles as $vehicle){
            $ret = DB::table('stays')->where('vehicle_id', $vehicle->id)->orderBy('created_at', 'desc')->limit(1)->value('created_at');
            $vehicle['last_stay'] = $ret;
            $nVeh[] = $vehicle;
        }
        return $nVeh;
    }

    public function getByID($vehicleID){
        return Vehicle::where(['user_id' => Auth::user()->id])->findOrFail($vehicleID);
    }

    public function insert(Request $request){
        $vehicle = new Vehicle;
        $this->validate($request, [
            'plate' => 'required|min:7|max:7',
            'name' => 'required'
        ]);
        $vehicle->fill($request->all());
        $vehicle->user_id = Auth::user()->id;
        $vehicle->save();
        return $vehicle;
    }

    public function delete(Request $request, $vehicleID){
        $vehicle = Vehicle::findOrFail($vehicleID);
        $vehicle->delete();
        return ["ok" => true];
    }
}
