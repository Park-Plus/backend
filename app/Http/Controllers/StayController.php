<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Stay;
use App\Models\User;
use App\Models\Vehicle;
use Cartalyst\Stripe\Laravel\Facades\Stripe;
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

    public function lasts($onlyEnded = true)
    {
        $conditions = ['user_id' => Auth::user()->id];
        if($onlyEnded) $conditions['status'] = 'ended';
        $stays = Stay::where($conditions)->get();
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

    public function start(Request $request){
        $this->validate($request, [
            'plate' => 'required|min:7|max:7'
        ]);
        $vehicle = Vehicle::where('plate', $request->get('plate'))->firstOrFail();
        $user = User::findOrFail($vehicle->user_id);
        $alreadyStaying = Stay::where(["user_id" => $user->id, "vehicle_id" => $vehicle->id, "status" => "active"])->get();
        $unpaidInvoice = Invoice::where(["user_id" => $user->id, "status" => "unpaid"])->get();
        if(count($alreadyStaying) == 0){
            $stay = new Stay;
            $stay->user_id = $user->id;
            $stay->vehicle_id = $vehicle->id;
            $stay->status = "active";
            $stay->invoice_id = null;
            $stay->save();
            return ["ok" => true, "data" => $stay];
        }elseif(count($unpaidInvoice) > 0){
            return ["ok" => false, "data" => "User has an unpaid invoice"];
        }else{
            return ["ok" => false, "data" => "User is already in the parking"];
        }

    }

    public function end(Request $request){
        $this->validate($request, [
            'plate' => 'required|min:7|max:7'
        ]);
        $vehicle = Vehicle::where('plate', $request->get('plate'))->firstOrFail();
        $user = User::findOrFail($vehicle->user_id);
        $stay = Stay::where(['user_id' => $user->id, 'vehicle_id' => $vehicle->id, 'status' => 'active'])->firstOrFail();
        $stay->status = "ended";
        $stay->save();
        $start = strtotime($stay->created_at);
        $end = strtotime($stay->updated_at);
        $stayTime = ($end - $start) / 60;
        $invoicePrice = round($stayTime * Stay::PRICE_PER_MINUTE, 2) + 0.5;
        $inv = new Invoice;
        $inv->user_id = $user->id;
        $inv->price = $invoicePrice;
        $inv->status = "unpaid";
        $inv->save();
        $stay->invoice_id = $inv->id;
        $stay->save();
        $charge = Stripe::charges()->create([
            'customer' => $user->stripe_user_id,
            'currency' => 'EUR',
            'amount'   => $invoicePrice,
            'description' => "Park+ automatic charge for stay #" . $stay->id . " on " . date('d/m/Y'),
            'statement_descriptor' => "P+",
            'statement_descriptor_suffix' => "PARK+: " . date('d/m')
        ]);
        if($charge['status'] == "succeeded"){
            $inv->status = "paid";
            $inv->stripe_payment_id = $charge["id"];
            $inv->date_paid = date('Y-m-d h:m:s', time());
            $inv->save();
        }
        return ["ok" => true, "inv_status" => $inv->status];
    }
}
