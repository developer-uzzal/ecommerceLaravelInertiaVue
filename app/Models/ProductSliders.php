<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSliders extends Model
{
    protected $fillable = [
        'product_id', 'image', 'title', 'short_des', 'price','status'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

}
