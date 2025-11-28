<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'product_id' => $this->product_id,
            'color'      => $this->color,
            'size'       => $this->size,
            'weight'     => $this->weight,
            'price_mrp'  => $this->price_mrp,
            'price_sp'   => $this->price_sp,
            'stock'      => $this->stock,
            'sku'        => $this->sku,

            // Relations
            'images'     => ProductImageResource::collection($this->whenLoaded('images')),

            // Product (optional)
            'product'    => new ProductResource($this->whenLoaded('product')),
        ];
    }
}
