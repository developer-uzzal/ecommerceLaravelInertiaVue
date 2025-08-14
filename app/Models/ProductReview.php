<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductReview extends Model
{
    protected $fillable = [
        'user_id', 'product_id', 'rating', 'des'
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
