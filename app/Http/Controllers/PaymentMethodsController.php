<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Stay;
use App\Models\User;
use App\Models\Vehicle;
use Cartalyst\Stripe\Laravel\Facades\Stripe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PaymentMethodsController extends Controller
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
        $defaultPaymentMethod = Stripe::customers()->find(Auth::user()->stripe_user_id)["default_source"];
        $cards = Stripe::cards()->all(Auth::user()->stripe_user_id)['data'];
        $nCards = [];
        foreach($cards as $card){
            $nCard = $card;
            if($card["id"] == $defaultPaymentMethod){
                $nCard["default"] = true;
            }else{
                $nCard["default"] = false;
            }
            $nCards[] = $nCard;
        }
        return $nCards;
    }

    public function add(Request $request){
        $this->validate($request, [
            'card_number' => new \LVR\CreditCard\CardNumber,
            'expiration_month' => ['required', new \LVR\CreditCard\CardExpirationMonth($request->get('expiration_year'))],
            'expiration_year' => new \LVR\CreditCard\CardExpirationYear($request->get('expiration_month')),
            'cvc' => new \LVR\CreditCard\CardCvc($request->get('card_number'))
        ]);
        $tok = Stripe::tokens()->create([
            'card' => [
                'number'    => $request->get('card_number'),
                'exp_month' => $request->get('expiration_month'),
                'exp_year'  => $request->get('expiration_year'),
                'cvc'       => $request->get('cvc'),
            ]
        ]);
        $card = Stripe::cards()->create(Auth::user()->stripe_user_id, $tok['id']);
        return Stripe::cards()->find(Auth::user()->stripe_user_id, $card['id']);
    }

    public function setDefault(Request $request){
        $this->validate($request, [
            'card_id' => 'required'
        ]);
        return Stripe::customers()->update(Auth::user()->stripe_user_id, [
            'default_source' => $request->get('card_id')
        ]);
    }

    public function delete($cardID){
        return Stripe::cards()->delete(Auth::user()->stripe_user_id, $cardID);
    }

}
