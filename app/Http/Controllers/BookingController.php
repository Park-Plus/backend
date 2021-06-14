<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Place;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {

    }

    public function list(){
        $bookings = Booking::where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->get();
        return $bookings;
    }

    public function available(Request $request){
        $this->validate($request, [
            'start' => 'required',
            'end' => 'required|greater_than_field:start',
        ]);
        $start = date("Y-m-d H:i:s", $request->input("start"));
        $end = date("Y-m-d H:i:s", $request->input("end"));
        $freePlaces = Place::where('status', 'free')->get();
        $availablePlaces = [];
        foreach($freePlaces as $place){
            $alreadyExistingBookings = Booking::where(function($query) use ($start, $end){
                $query->whereBetween('start', [$start, $end])
                ->orWhereBetween('end', [$start, $end]);
            })->where('place_id', $place->id)->count();
            if($alreadyExistingBookings == 0){
                $previousBookingExists = Booking::where(function($query) use ($start, $end){
                    $query->where('start', '<', $start)
                    ->where('end', '<', $start);
                })->where('place_id', $place->id)->count();
                if($previousBookingExists > 0){
                    $place["previousBookings"] = ["count" => $previousBookingExists];
                }
                $availablePlaces[] = $place;
            }
        }
        return $availablePlaces;
    }

    public function book(Request $request){
        $this->validate($request, [
            "place" => "required|min:2|max:2",
            'start' => 'required',
            'end' => 'required|greater_than_field:start',
        ]);
        $start = date("Y-m-d H:i:s", $request->input("start"));
        $end = date("Y-m-d H:i:s", $request->input("end"));
        $place = Place::where('status', 'free')->where(["section" => substr($request->input('place'), 0, 1), "number" => substr($request->input('place'), 1, 1)])->firstOrFail();
        $alreadyExistingBookings = Booking::where(function($query) use ($start, $end){
            $query->whereBetween('start', [$start, $end])
            ->orWhereBetween('end', [$start, $end]);
        })->where('place_id', $place->id)->count();
        if($alreadyExistingBookings == 0){
            $booking = new Booking;
            $booking->user_id = Auth::user()->id;
            $booking->status = 'pending';
            $booking->start = $start;
            $booking->end = $end;
            $booking->place_id = $place->id;
            $booking->save();
            return $booking;
        }else{
            abort(403, 'Place not available for given timespan');
        }
    }

    public function delete($bookingId){
        $booking = Booking::findOrFail($bookingId);
        $booking->status = "canceled";
        $booking->save();
        return [];
    }
}
