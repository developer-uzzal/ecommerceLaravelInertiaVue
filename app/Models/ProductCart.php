<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCart extends Model
{
    protected $fillable = [
        'user_id', 'product_id', 'color', 'size', 'qty', 'price'
    ];

    public function user()
{
    return $this->belongsTo(User::class);
}

public function products()
{
    return $this->belongsTo(Product::class);
}


}
