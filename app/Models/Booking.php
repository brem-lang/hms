<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $guarded = [];

    protected $casts = [
        'additional_charges' => 'array',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function suiteRoom()
    {
        return $this->belongsTo(SuiteRoom::class);
    }

    public function walkingGuest()
    {
        return $this->hasOne(WalkinGuest::class);
    }

    public function relatedBookings()
    {
        return $this->hasMany(Booking::class, 'bulk_head_id');
    }
}
