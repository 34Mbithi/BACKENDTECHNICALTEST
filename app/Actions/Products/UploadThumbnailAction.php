<?php

namespace App\Actions\Products;

use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UploadThumbnailAction
{
    /**
     * Upload and store product thumbnail.
     */
    public function execute(Product $product, UploadedFile $file): Product
    {
        // Delete old thumbnail if it exists
        if ($product->thumbnail_path && Storage::disk('public')->exists($product->thumbnail_path)) {
            Storage::disk('public')->delete($product->thumbnail_path);
        }

        // Store new thumbnail
        $filename = 'thumbnail.' . $file->extension();
        $path = $file->storeAs(
            "products/{$product->id}",
            $filename,
            'public'
        );

        $product->update(['thumbnail_path' => $path]);

        return $product;
    }
}
