<?php

namespace App\Console;

use App\Libraries\PaymentsHelper;
use App\Models\Booking;
use App\Models\Invoice;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Log;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \Laravelista\LumenVendorPublish\VendorPublishCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            $expiredBookings = Booking::whereDate('end', '<', Carbon::now())->get();
            foreach($expiredBookings as $booking){
                Log::info("[BOOKING " . $booking->id . "] Setting as terminated...");
                $booking->status = "ended";
                $booking->save();
                $user = User::find($booking->user_id);
                if($booking->stay_id == null && $user->plan == "free"){
                    Log::info("[BOOKING " . $booking->id . "] Attempt booking payment...");
                    $invoicePrice = Booking::PRICE_PER_MINUTE_BOOKING * round((strtotime($booking->end) - strtotime($booking->created_at))/60);
                    $inv = new Invoice();
                    $inv->user_id = $user->id;
                    $inv->price = $invoicePrice;
                    $inv->status = 'unpaid';
                    $inv->save();
                    $charge = PaymentsHelper::generatePayment($user->stripe_user_id, Booking::PRICE_PER_MINUTE_BOOKING * round((strtotime($booking->end) - strtotime($booking->created_at))/60), "Park+ automatic charge for booking #".$booking->id);
                    if ($charge['status'] == 'succeeded') {
                        $inv->status = 'paid';
                        $inv->stripe_payment_id = $charge['id'];
                        $inv->date_paid = date('Y-m-d h:m:s', time());
                        $inv->save();
                    }
                }
            }
        })->everyFiveMinutes();
    }
}
