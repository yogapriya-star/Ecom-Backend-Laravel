<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\AssignProductFilterRequest;
use App\Http\Resources\ProductFilterResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductFilterController extends Controller
{
    /**
     * Assign filter options to a product
    */ 
    public function assign(AssignProductFilterRequest $request, Product $product)
    {
        // Assign filter options
        $product->filterOptions()->sync($request->filter_option_ids);

        // Eager load filterOptions and variants
        $product->load(['filterOptions.filter', 'variants']);

        return response()->json([
            'message' => 'Filter options assigned successfully',
            'product' => new ProductFilterResource($product)
        ]);
    }

    /**
     * Filter products with query parameters
    */
    public function filter(Request $request, Product $product)
    {
        // Eager load variants and filter options
        $product->load(['variants', 'filterOptions.filter']);

        $activeFilters = $request->query();

        // Normalize query parameters
        foreach ($activeFilters as $key => $value) {
            if (!is_array($value)) {
                $activeFilters[$key] = explode(',', $value); // handle comma-separated values
            }
        }

        // Build selected_filters for frontend
        $selectedFilters = [];
        foreach ($product->filterOptions as $opt) {
            $filterName = $opt->filter->name;
            $selectedFilters[$filterName] = $activeFilters[$filterName] ?? [];
        }

        // Pass active filters as query so the Resource filters variants correctly
        return (new ProductFilterResource($product->setRelation('active_filters', $activeFilters)))
            ->additional(['selected_filters' => $selectedFilters]);
    }
}
