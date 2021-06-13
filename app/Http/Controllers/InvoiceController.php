<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Stay;
use App\Models\User;
use Cartalyst\Stripe\Laravel\Facades\Stripe;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {

    }

    public function list(){
        $invoices = Auth::user()->invoices;
        $unpaidTotal = 0;
        foreach($invoices as $invoice){
            $stop = Stay::where("invoice_id", $invoice->id)->first();
            $relation = ["type" => "stop", "date" => substr($stop->created_at, 0, 10)];
            $invoice["relation"] = $relation;
            if($invoice["status"] == "unpaid"){
                $unpaidTotal = $unpaidTotal + $invoice["price"];
            }
        }
        return ["unpaid_total" => $unpaidTotal, "invoices" => $invoices];
    }

    public function tryUnpaid(){
        $invoices = Auth::user()->invoices->where('status', 'unpaid');
        $unpaidCount = count($invoices);
        $paidCount = 0;
        foreach($invoices as $invoice){
            $charge = Stripe::charges()->create([
                'customer' => Auth::user()->stripe_user_id,
                'currency' => 'EUR',
                'amount'   => $invoice->price,
                'description' => "Park+ automatic charge",
                'statement_descriptor' => "P+",
                'statement_descriptor_suffix' => "PARK+: " . date('d/m')
            ]);
            if($charge['status'] == "succeeded"){
                $invoice->status = "paid";
                $invoice->stripe_payment_id = $charge["id"];
                $invoice->date_paid = date('Y-m-d h:m:s', time());
                $invoice->save();
                $paidCount++;
            }
        }
        return ["unpaids" => $unpaidCount, "paid" => $paidCount, "invoices" => $invoices];
    }
}