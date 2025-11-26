<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\Auth\StoreCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Requests\Auth\UpdateCategoryRequest;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Get categories
    */
    public function index()
    {
        $categories = Category::with('children')
            ->orderBy('position')
            ->get();

        return CategoryResource::collection($categories);
    }

    /**
     * Create categories
    */
    public function store(StoreCategoryRequest $request)
    {
        $data = $request->validated();
        // If slug not provided → generate automatically from name
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        // Ensure slug is unique (in case auto-generated duplicates)
        $originalSlug = $data['slug'];
        $counter = 1;
        while (\App\Models\Category::where('slug', $data['slug'])->exists()) {
            $data['slug'] = $originalSlug . '-' . $counter++;
        }
        $category = Category::create($data);
       
        return new CategoryResource($category);
    }

    /**
     *  GET categories by id
    */
    public function show(Category $category)
    {
        return new CategoryResource($category->load('children'));
    }

    /**
     * Update categories
    */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $data = $request->validated();

        // Prevent a category from being its own parent
        if (isset($data['parent_id']) && $data['parent_id'] == $category->id) {
            return response()->json([
                'status' => false,
                'message' => 'A category cannot be its own parent.'
            ], 422);
        }

        // STEP 1: Global name validation (all levels)
        $nameExists = Category::where('name', $data['name'])
            ->where('id', '!=', $category->id)
            ->exists();

        if ($nameExists) {
            return response()->json([
                'status' => false,
                'message' => 'Category name already exists in another level.'
            ], 422);
        }

        //  STEP 2: Auto-generate slug if empty
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        // Store original slug for auto-increment fallback
        $originalSlug = $data['slug'];
        $counter = 1;

        // STEP 3: Make slug unique (global)
        while (
            Category::where('slug', $data['slug'])
            ->where('id', '!=', $category->id)
            ->exists()
        ) {
            $data['slug'] = $originalSlug . '-' . $counter++;
        }

        // STEP 4: Update category
        $category->update($data);

        return response()->json([
            'status' => true,
            'message' => 'Category updated successfully',
            'data' => new CategoryResource($category)
        ]);
    }

    /**
     * Delete categories
    */
    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'status' => false,
                'message' => 'Category not found',
            ], 404);
        }

        $category->delete();

        return response()->json([
            'status' => true,
            'message' => 'Category deleted successfully',
        ]);
    }

    /**
     * Get all top-level categories
    */
    public function categoryTree()
    {
        // Fetch all top-level categories with nested children recursively
        $categories = Category::whereNull('parent_id')
            ->with('sub_categories') // recursive relation
            ->orderBy('position')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Category tree fetched successfully',
            'data' => $categories
        ]);
    }

    /**
     * Get only active top-level categories
    */
    public function parentCategories()
    {
        // Fetch only active top-level categories
        $categories = Category::where('is_active', 1)
            ->whereNull('parent_id') // only top-level categories
            ->orderBy('position')
            ->get(['id', 'name']);

        return response()->json([
            'status' => true,
            'message' => 'Parent categories fetched successfully',
            'data' => $categories
        ]);
    }

    /**
     * Get only active 2nd-level categories
    */
    public function secondLevelCategories()
    {
        // Fetch categories whose parent is not null but parent's parent is null (2nd level)
        $categories = Category::whereHas('parent', function($query) {
                $query->whereNull('parent_id'); // parent is top-level
            })
            ->where('is_active', 1)
            ->orderBy('position')
            ->get(['id', 'name', 'parent_id']);

        return response()->json([
            'status' => true,
            'message' => 'Second-level categories fetched successfully',
            'data' => $categories
        ]);
    }

}
