<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'short_des',
        'long_des',
        'base_price',
        'sale_price',
        'discount',
        'image',
        'stock',
        'trending',
        'featured',
        'best_selling',
        'is_active',
        'category_id',
        'brand_id'
    ];



    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
    public function colors()
    {
        return $this->belongsToMany(Color::class, 'product_colors');
    }

    public function sizes()
    {
        return $this->belongsToMany(Size::class, 'product_sizes');
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function sliders()
    {
        return $this->hasMany(ProductSliders::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }


    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    public function carts()
    {
        return $this->hasMany(ProductCart::class);
    }

    public function wishes()
    {
        return $this->hasMany(ProductWish::class);
    }

    public function invoiceProducts()
    {
        return $this->hasMany(InvoiceProduct::class);
    }
}
