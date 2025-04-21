<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $guarded = [];

    protected $casts = [
        'items' => 'array',
    ];

    public function suite_rooms()
    {
        return $this->hasMany(SuiteRoom::class);
    }

    public function suite_rooms_available()
    {
        return $this->hasMany(SuiteRoom::class)->where('is_occupied', false);
    }
}
