<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Stay;
use App\Models\User;
use App\Models\Vehicle;
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

    public function homePageWidgets(){
        $isStaying = Stay::where(["user_id" => Auth::user()->id, "status" => "active"])->get();
        $hasUnpaidInvoices = Invoice::where(["user_id" => Auth::user()->id, "status" => "unpaid"])->get();
        $cards = [];
        if(count($hasUnpaidInvoices) > 0){
            $nCard = [];
            $nCard["type"] = "UNPAID_INVOICE";
            $nCard["title"] = "Fattur" . ((count($hasUnpaidInvoices) != 1) ? "e" : "a"). " non pagat".((count($hasUnpaidInvoices) != 1) ? "e" : "a");
            $nCard["data"] = ["unpaid_count" => count($hasUnpaidInvoices), "invoice" => $hasUnpaidInvoices];
            $cards[] = $nCard;
        }
        if(count($isStaying) > 0){
            $nCard = [];
            $nCard["type"] = "ONGOING_STAY";
            $nCard["title"] = "Sosta in corso";
            $nCard["data"] = ["vehicle" => Vehicle::find($isStaying[0]->vehicle_id)->first(), "stay" => $isStaying];
            $cards[] = $nCard;
        }
        if(Auth::user()->plan == "free"){
            $nCard = [];
            $nCard["type"] = "SWITCH_TO_PREMIUM";
            $nCard["title"] = "Passa a premium";
            $nCard["data"] = [];
            $cards[] = $nCard;
        } // TODO Usare un trait
        $nCard = [];
        $nCard["type"] = "NEXT_MILESTONES";
        $nCard["title"] = "Qualche spoiler 🚀";
        $nCard["data"] = [];
        $cards[] = $nCard;
        return $cards;
    }

    public function edit()
    {
        // TODO
    }
}
