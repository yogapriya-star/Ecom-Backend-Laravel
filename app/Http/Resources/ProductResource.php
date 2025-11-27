<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'category' => $this->category ? $this->category->name : null,
            'name' => $this->name,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'price_mrp' => $this->price_mrp,
            'price_sp' => $this->price_sp,
            'discount_percent' => $this->discount_percent,
            'description' => $this->description,
            'short_description' => $this->short_description,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'is_featured' => $this->is_featured,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
