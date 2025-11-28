<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductImage;
use App\Http\Requests\Auth\StoreProductVariantRequest;
use App\Http\Requests\Auth\UpdateProductVariantRequest;
use App\Http\Resources\ProductVariantResource;

class ProductVariantController extends Controller
{
    /**
     * List all variants of a product
     */
    public function index($productId)
    {
        $product = Product::find($productId);
        if (!$product) {
            return response()->json(['status'=>false,'message'=>'Product not found'],404);
        }

        $variants = $product->variants()->with('images')->get();
        return ProductVariantResource::collection($variants);
    }

    /**
     * Create a new product variant
     */
    public function store(StoreProductVariantRequest $request)
    {
        $data = $request->validated();
        $product = Product::find($data['product_id']);
        if (!$product) {
            return response()->json(['status'=>false,'message'=>'Product not found'],404);
        }

        $variant = ProductVariant::create($data);
        return new ProductVariantResource($variant);
    }

    /**
     * Show single variant
     */
    public function show(Product $product, $variantId)
    {
        $variant = ProductVariant::with('images', 'product')
            ->where('product_id', $product->id)
            ->where('id', $variantId)
            ->first();

        if (!$variant) {
            return response()->json([
                'status' => false,
                'message' => 'Variant not found'
            ], 404);
        }

        return new ProductVariantResource($variant);
    }


    /**
     * Update variant
     */
    public function update(UpdateProductVariantRequest $request, $id)
    {
        $variant = ProductVariant::find($id);
        if (!$variant) {
            return response()->json(['status'=>false,'message'=>'Variant not found'],404);
        }

        $variant->update($request->validated());
        return new ProductVariantResource($variant);
    }

    /**
     * Delete variant and its images
     */
    public function destroy($variantId)
    {
        $variant = ProductVariant::find($variantId);
  
        if (!$variant) {
            return response()->json(['status'=>false,'message'=>'Variant not found'],404);
        }

        // Delete related images
        foreach ($variant->images as $image) {
            if ($image->path && \Storage::disk('public')->exists($image->path)) {
                \Storage::disk('public')->delete($image->path);
            }
            $image->delete();
        }

        $variant->delete();
        return response()->json(['status'=>true,'message'=>'Variant deleted']);
    }
}
