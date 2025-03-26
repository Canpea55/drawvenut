<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Library extends Model
{
    protected $table = 'library';
    protected $fillable = [
        'user_id', 
        'product_options_id', 
    ];

    use HasFactory;
}
