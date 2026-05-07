<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'customer_id',
        'quantity',
        'total',
        'status' //Pending, Shipped, Delivered
    ];

    public function shoes()
    {
        return $this->belongsToMany(Shoe::class)
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function payments()
    {
        return $this->hasOne(Payment::class);
    }
}
