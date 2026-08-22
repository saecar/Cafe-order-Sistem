<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class order extends Model
{
    protected $fillable = [
        'order_number',
        'customer_name',
        'email',
        'phone',
        'address',
        'total_amount',
        'status',
    ];

    public function orderItems()
    {
        return $this->hasMany(order_item::class);
    }

    public function payment()
    {
        return $this->hasOne(payment::class);
    }
}