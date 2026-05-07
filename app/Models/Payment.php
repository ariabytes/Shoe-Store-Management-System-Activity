<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'paid_amount',
        'status' // Partial, Paid, Unpaid
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
