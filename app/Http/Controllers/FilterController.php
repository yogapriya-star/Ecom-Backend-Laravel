<?php

namespace App\Http\Controllers;

use App\Http\Resources\FilterResource;
use App\Http\Resources\ProductFilterResource;
use App\Http\Requests\Auth\StoreFilterRequest;
use App\Http\Requests\Auth\UpdateFilterRequest;
use App\Models\Filter;
use App\Models\Product;
use Illuminate\Http\Request;

class FilterController extends Controller
{
    /**
     * List all filters with options (frontend)
    */ 
    public function index() {
        return FilterResource::collection(Filter::with('options')->get());
    }

    /**
     * Admin: CRUD
    */
    /**
     * Filter: store
    */
    public function store(StoreFilterRequest $request) {
        $filter = Filter::create($request->validated());
        return new FilterResource($filter->load('options'));
    }

    /**
     * Filter: update
    */
    public function update(UpdateFilterRequest $request, Filter $filter) {
        $filter->update($request->validated());
        return new FilterResource($filter->load('options'));
    }

    /**
     * Filter: destroy
    */
    public function destroy($id)
    {
        $filter = Filter::find($id);

        if (!$filter) {
            return response()->json([
                'status' => false,
                'message' => 'Filter not found',
            ], 404);
        }

        $filter->delete();

        return response()->json([
            'status' => true,
            'message' => 'Filter deleted successfully',
        ]);
    }

    /**
     * Filter products dynamically
    */
    public function filteredProducts(Request $request)
    {
        $filters = $request->get('filters', []);

        $query = Product::query()
            ->with([
                'images',
                'variants',
                'filterOptions.filter'
            ]);

        foreach ($filters as $filterId => $optionIds) {
            $query->whereHas('filterOptions', function($q) use ($filterId, $optionIds) {
                $q->where('filter_id', $filterId)
                ->whereIn('filter_option_id', $optionIds);
            });
        }

        return ProductFilterResource::collection($query->get());
    }

}
