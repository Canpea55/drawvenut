<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductOptionDownloads extends Model
{
    use HasFactory;

    protected $table = 'product_option_downloads';

    protected $fillable = [
        'product_options_id', 
        'type', 
        'link',
        'isDownload',
    ];

    public function option()
    {
        return $this->belongsTo(ProductOptions::class);
    }
}
