<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerProfile extends Model
{
    protected $fillable = [
        'user_id',
        'address',
        'city',
        'state',
        'postcode',
        'country',
        'phone',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'customer_id');
    }
}
