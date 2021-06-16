<?php

namespace App\Libraries;

use App\Models\User;
use Cartalyst\Stripe\Laravel\Facades\Stripe;

define('SIGN_KEY', env('JWT_SECRET', false));

class PaymentsHelper
{
    public static function generatePayment($client_id, $amount, $reason){
        return Stripe::charges()->create([
            'customer' => $client_id,
            'currency' => 'EUR',
            'amount' => $amount,
            'description' => $reason,
            'statement_descriptor' => 'P+',
            'statement_descriptor_suffix' => 'PARK+: ' . date('d/m'),
        ]);
    }
}
