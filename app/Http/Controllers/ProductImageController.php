<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductImage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Auth\StoreProductImageRequest;
use App\Http\Requests\Auth\UpdateProductImageRequest;
use App\Http\Resources\ProductImageResource;

class ProductImageController extends Controller
{
    /**
     * List all images for a product (grouped by variant)
     */
    public function index(Product $product)
    {
        $variants = $product->variants()
            ->with(['images' => function ($q) {
                $q->orderBy('position');
            }])
            ->get()
            ->map(function ($variant) {
                // Convert images using your resource
                $variant->images = ProductImageResource::collection($variant->images);
                return $variant;
            });

        return response()->json([
            'status' => true,
            'product_id' => $product->id,
            'variants' => $variants
        ]);
    }

    /**
     * Store images for variant
     */
   public function store(StoreProductImageRequest $request)
    {
        $data = $request->validated();
        $variant = ProductVariant::findOrFail($data['product_variant_id']);

        // Get uploaded files safely
        $images = $request->file('images');

        if (!$images) {
            return response()->json([
                'status' => false,
                'message' => 'No images uploaded'
            ], 422);
        }

        // Ensure $images is always an array
        $images = is_array($images) ? $images : [$images];

        $storedImages = [];

        DB::transaction(function () use ($variant, $images, $data, &$storedImages) {
            $existingCount = $variant->images()->count();
            $hasPrimary = $variant->images()->where('is_primary', 1)->exists();

            foreach ($images as $index => $file) {
                $path = $file->store(
                    "products/{$variant->product_id}/variants/{$variant->id}",
                    'public'
                );

                $isPrimary = (!$hasPrimary && $existingCount === 0 && $index === 0);

                $image = ProductImage::create([
                    'product_id'         => $variant->product_id,
                    'product_variant_id' => $variant->id,
                    'path'               => $path,
                    'alt_text'           => $data['alt_text'] ?? null,
                    'position'           => $existingCount + $index,
                    'is_primary'         => $isPrimary,
                ]);

                $storedImages[] = new ProductImageResource($image);
            }
        });

        return response()->json([
            'status' => true,
            'message' => 'Images uploaded successfully',
            'data' => $storedImages
        ]);
    }

    /**
     * Update image: alt text or replace image file
     */
    public function update(UpdateProductImageRequest $request, $id)
    {
        $image = ProductImage::find($id);

        if (!$image) {
            return response()->json([
                'status' => false,
                'message' => 'Image not found'
            ], 404);
        }

        $data = $request->validated();

        // Handle new file
        if ($request->hasFile('image')) {

            // Delete old image
            if ($image->path) {
                $fullPath = public_path('storage/' . $image->path);
                if (file_exists($fullPath)) unlink($fullPath);
            }

            // Store new image
            $data['path'] = $request->file('image')->store(
                "products/{$image->product_id}/variants/{$image->product_variant_id}",
                'public'
            );
        }

        // Update DB
        $image->update($data);

        return new ProductImageResource($image);
    }

    /**
     * Set an image as primary for the variant
     */
    public function setPrimary(Product $product, ProductImage $image)
    {
        $variantId = $image->product_variant_id;

        DB::transaction(function () use ($image, $variantId) {
            // make all images for variant non-primary
            ProductImage::where('product_variant_id', $variantId)
                ->update(['is_primary' => false]);

            $image->update(['is_primary' => true]);
        });

        return response()->json([
            'status' => true,
            'message' => 'Primary image updated successfully',
            'data' => new ProductImageResource($image)
        ]);
    }

    /**
     * Reorder images for a variant
     */
    public function reorder(Product $product)
    {
        $order = request()->input('order');

        if (!is_array($order)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid or missing order data'
            ], 422);
        }

        foreach ($order as $item) {
            ProductImage::where('id', $item['id'])->update([
                'position' => $item['position']
            ]);
        }

        return response()->json(['status' => true, 'message' => 'Reordered successfully']);
    }

    /**
     * Delete image. If primary, assign next image as primary.
     */
    public function destroy($id)
    {
        $image = ProductImage::find($id);

        if (!$image) {
            return response()->json(['status'=>false,'message'=>'Image not found'],404);
        }

        $variantId = $image->product_variant_id;
        $wasPrimary = $image->is_primary;

        // Delete file
        if ($image->path && Storage::disk('public')->exists($image->path)) {
            Storage::disk('public')->delete($image->path);
        }

        $image->delete();

        // If deleted image was primary — assign next image
        if ($wasPrimary) {
            $next = ProductImage::where('product_variant_id', $variantId)
                ->orderBy('position')
                ->first();

            if ($next) {
                $next->update(['is_primary' => true]);
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Image deleted successfully'
        ]);
    }
}
