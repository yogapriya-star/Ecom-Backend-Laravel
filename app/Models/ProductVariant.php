<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class ProductVariant extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'product_id',
        'sku',
        'variant_label',
        'color',
        'size',
        'additional_price',
        'stock',
        'is_active'
    ];

    // Relations
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_variant_id');
    }
}
