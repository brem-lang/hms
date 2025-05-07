<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodOrder extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function food()
    {
        return $this->belongsTo(Food::class);
    }
}
