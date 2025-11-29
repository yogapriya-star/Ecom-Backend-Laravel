<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductFilterResource extends JsonResource
{
    protected $request;

    public function __construct($resource, $request = null)
    {
        parent::__construct($resource);
        $this->request = $request;
    }

    public function toArray($request)
    {
        $activeFilters = $this->request ? $this->request->query() : [];

        // Filter variants
        $variants = $this->variants->filter(function ($variant) use ($activeFilters) {
            foreach ($activeFilters as $filterName => $filterValues) {
                // Skip pagination params
                if (in_array($filterName, ['page', 'limit'])) continue;

                $filterValuesArray = is_array($filterValues)
                    ? $filterValues
                    : explode(',', $filterValues);

                $column = strtolower($filterName); // must match variant column
                if (!in_array($variant->$column, $filterValuesArray)) {
                    return false; // AND logic across filters
                }
            }
            return true;
        });

        // Skip product if no variants match
        if ($variants->isEmpty()) return null;

        // Transform variants with primary image
        $variantsTransformed = $variants->map(function ($v) {
            $primaryImage = $v->images->firstWhere('is_primary', true);
            return [
                'id'    => $v->id,
                'sku'   => $v->sku,
                'color' => $v->color ?? null,
                'size'  => $v->size ?? null,
                'image' => $primaryImage ? [
                    'id'        => $primaryImage->id,
                    'path'      => $primaryImage->path,
                    'url'       => $primaryImage->url,
                    'alt_text'  => $primaryImage->alt_text,
                    'position'  => $primaryImage->position,
                    'is_primary'=> $primaryImage->is_primary,
                ] : null
            ];
        });

        // Build filters
        $filters = $this->filterOptions
            ->groupBy('filter.name')
            ->map(fn($items) => $items->pluck('value'));

        // Selected filters
        $selectedFilters = [];
        foreach ($activeFilters as $key => $value) {
            if (in_array($key, ['page','limit'])) continue;
            $selectedFilters[$key] = is_array($value) ? $value : explode(',', $value);
        }

        return [
            'id'               => $this->id,
            'name'             => $this->name,
            'variants'         => $variantsTransformed,
            'filters'          => $filters,
            'selected_filters' => $selectedFilters,
        ];
    }
}
