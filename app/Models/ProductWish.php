<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductWish extends Model
{
    protected $fillable = [
        'customer_id', 'product_id'
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
