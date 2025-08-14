<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'customer_id',
        'name',
        'invoice_no',
        'total',
        'vat',
        'payable',
        'ship_add',
        'ship_city',
        'ship_country',
        'ship_state',
        'ship_phone',
        'ship_mail',
        'delivery_status',
        'payment_method',
        'payment_status',
        'order_status',
        'shipping_method_id',
        'note'
    ];

//     public function user()
// {
//     return $this->belongsTo(User::class);
// }

public function products()
{
    return $this->hasMany(InvoiceProduct::class);
}

public function items()
{
    return $this->hasMany(InvoiceProduct::class);
}

public function customer()
{
    return $this->belongsTo(CustomerProfile::class);
}
public function shippingMethod()
{
    return $this->belongsTo(ShippingMethod::class, 'shipping_method_id');
}

}
