<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceProduct extends Model
{
    protected $fillable = [
        'invoice_id',
        'product_id',
        'user_id',
        'qty',
        'sale_price',
        'color',
        'size',
    ];

    public function invoice()
{
    return $this->belongsTo(Invoice::class);
}

public function product()
{
    return $this->belongsTo(Product::class);
}

public function user()
{
    return $this->belongsTo(User::class);
}
}
