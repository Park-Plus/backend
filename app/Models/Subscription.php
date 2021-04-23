<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'plan_id', 'start_date', 'end_date'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];

    public function owner(){
        return $this->hasOne(User::class);
    }
    
    public function plan(){
        return $this->hasOne(Plan::class);
    }
}
