<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class order extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'order_number',
        'customer_name',
        'email',
        'phone',
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
