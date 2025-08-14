<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as AuthUser;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Model
{
    use HasApiTokens, HasFactory, Notifiable;
    
    protected $fillable = [
        'name', 'username', 'role', 'email', 'password', 'image','type'
    ];

    public function profile()
{
    return $this->hasOne(CustomerProfile::class);
}

public function reviews()
{
    return $this->hasMany(ProductReview::class);
}

public function wishes()
{
    return $this->hasMany(ProductWish::class);
}

public function carts()
{
    return $this->hasMany(ProductCart::class);
}

public function invoices()
{
    return $this->hasMany(Invoice::class);
}

public function invoiceProducts()
{
    return $this->hasMany(InvoiceProduct::class);
}
    
}
