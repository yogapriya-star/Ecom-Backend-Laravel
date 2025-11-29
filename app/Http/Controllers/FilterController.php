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
        $limit = $request->query('limit', 10);
        $page  = $request->query('page', 1);

        // Base query with relationships
        $query = Product::with([
            'variants.images',
            'filterOptions.filter'
        ]);

        // Paginate products
        $paginated = $query->paginate($limit, ['*'], 'page', $page);

        // Transform products and remove those with zero matching variants
        $filteredProducts = $paginated->getCollection()->map(function ($product) use ($request) {
            $resource = new ProductFilterResource($product, $request);
            // Return null if variants are empty
            return empty($resource['variants']) ? null : $resource;
        })->filter(); // Remove nulls

        // Replace paginator collection with filtered products
        $paginated->setCollection($filteredProducts);

        return response()->json($paginated);
    }

}
