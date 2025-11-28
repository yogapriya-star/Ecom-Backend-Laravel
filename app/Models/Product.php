<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'name', 'slug', 'sku',
        'price_mrp', 'price_sp', 'discount_percent',
        'description', 'short_description',
        'meta_title', 'meta_description',
        'is_featured', 'status'
    ];

    // Relations
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function filters()
    {
        return $this->belongsToMany(FilterOption::class, 'product_filter_values', 'product_id', 'filter_option_id');
    }
}

