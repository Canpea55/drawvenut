<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderCancelation extends Model
{
    use HasFactory;
    protected $table = 'order_cancel';
    protected $fillable = [
        'order_id',
        'user_id',
        'reason',
        'status',
    ];
}
