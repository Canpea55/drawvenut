<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id', 
        'product_id', 
        'product_name', 
        'product_options_id', 
        'option_thumbnail',
        'option_name',
        'option_price',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}

