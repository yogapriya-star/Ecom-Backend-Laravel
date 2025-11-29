<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\StoreFilterOptionRequest;
use App\Http\Requests\Auth\UpdateFilterOptionRequest;
use App\Http\Resources\FilterOptionResource;
use App\Models\Filter;
use App\Models\FilterOption;

class FilterOptionController extends Controller
{
     /**
     * List all Filter option
    */
    public function index(Filter $filter) {
        return FilterOptionResource::collection($filter->options);
    }

    /**
     * Filter option store
    */
    public function store(StoreFilterOptionRequest $request, Filter $filter) {
        $option = $filter->options()->create($request->validated());
        return new FilterOptionResource($option);
    }

    /**
     * Filter option update
    */
    public function update(UpdateFilterOptionRequest $request, FilterOption $option) {
        $option->update($request->validated());
        return new FilterOptionResource($option);
    }

    /**
     * Filter option destroy
    */
    public function destroy($id)
    {
        $option = FilterOption::find($id);

        if (!$option) {
            return response()->json([
                'status' => false,
                'message' => 'Filter option not found',
            ], 404);
        }

        $option->delete();

        return response()->json([
            'status' => true,
            'message' => 'Filter option deleted successfully',
        ]);
    }
}
