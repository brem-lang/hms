<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuiteRoom extends Model
{
    protected $guarded = [];

    protected $casts = [
        'items' => 'array',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
