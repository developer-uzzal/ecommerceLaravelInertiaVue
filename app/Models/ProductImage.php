<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $fillable = [
        'product_id', 'product_image'
    ];

    public function products()
{
    return $this->belongsTo(Product::class);
}

}
