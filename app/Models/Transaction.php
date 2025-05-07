<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $guarded = [];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function foodOrder()
    {
        return $this->belongsTo(FoodOrder::class);
    }
}
