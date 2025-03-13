<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Spatie\Tags\HasTags;

class Product extends Model
{
    use HasFactory, HasTags, Searchable;

    protected $fillable = [
        'name',
        'price',
        'description',
        'images',
        'isBuyable',
        'isDownloadable',
        'isPublish',
        'download',
    ];
}
