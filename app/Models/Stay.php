<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stay extends Model
{
    use HasFactory;
    public const PRICE_PER_MINUTE = 0.05;
    public const PRICE_PER_MINUTE_PREMIUM = 0.03;

    public const START_TOLERANCE = 5; // Tolerance for the stay start (e.g. User has booked at 10:00, it arrives at 09:55, booking is still considered)

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'vehicle_id', 'status', 'invoice_id',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
}
