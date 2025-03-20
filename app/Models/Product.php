<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'images',
        'isBuyable',
        'isDownloadable',
        'isPublish',
        'download',
    ];

    public function tags()
    {
        return $this->belongsToMany(\App\Models\Tag::class, 'product_tags', 'product_id', 'tag_id');
    }
}