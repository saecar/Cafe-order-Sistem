<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class products extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'image',
        'stock', // fix: sebelumnya 'strock'
        'is_active',
        'cost_price', // fix: sebelumnya tidak ada
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(categories::class);
    }

    public function orderItems()
    {
        return $this->hasMany(order_item::class);
    }
}