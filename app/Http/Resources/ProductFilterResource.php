<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductFilterResource extends JsonResource
{
    public function toArray($request)
    {
        $activeFilters = $request->query();

        // Normalize query parameters: each filter should be an array
        foreach ($activeFilters as $key => $value) {
            if (!is_array($value)) {
                $activeFilters[$key] = explode(',', $value);
            }
        }

        // Filter variants based on active filters
        $variants = $this->variants->filter(function ($variant) use ($activeFilters) {
            foreach ($activeFilters as $filterName => $filterValues) {
                $column = strtolower($filterName); // assumes variant column names match filter names
                if (!in_array($variant->$column, $filterValues)) {
                    return false;
                }
            }
            return true;
        });

        // Build selected_filters for frontend
        $selectedFilters = [];
        foreach ($this->filterOptions as $opt) {
            $filterName = $opt->filter->name;
            $selectedFilters[$filterName] = $activeFilters[$filterName] ?? [];
        }

        return [
            'id'       => $this->id,
            'name'     => $this->name,

            // Variants with primary image
            'variants' => $variants->values()->map(function ($v) {
                $primaryImage = $v->images->firstWhere('is_primary', true);

                return [
                    'id'    => $v->id,
                    'sku'   => $v->sku,
                    'color' => $v->color ?? null,
                    'size'  => $v->size ?? null,
                    'image' => $primaryImage ? [
                        'id'         => $primaryImage->id,
                        'path'       => $primaryImage->path,
                        'url'        => $primaryImage->url,
                        'alt_text'   => $primaryImage->alt_text,
                        'position'   => $primaryImage->position,
                        'is_primary' => true,
                    ] : null,
                ];
            }),

            // All available filters
            'filters' => $this->filterOptions
                ->groupBy('filter.name')
                ->map(function ($items, $filterName) use ($activeFilters) {
                    if (isset($activeFilters[$filterName])) {
                        $filterValues = (array) $activeFilters[$filterName];
                        return $items->whereIn('value', $filterValues)->pluck('value');
                    }
                    return $items->pluck('value');
                }),

            // Selected filters applied in query
            'selected_filters' => $selectedFilters,
        ];
    }
}
