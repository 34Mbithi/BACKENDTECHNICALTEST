<?php

namespace App\Actions\Products;

use App\Data\ProductData;
use App\Models\Product;

class UpdateProductAction
{
    /**
     * Update an existing product.
     */
    public function execute(Product $product, ProductData $data): Product
    {
        $updateData = [];
        
        if ($data->title !== null) $updateData['title'] = $data->title;
        if ($data->description !== null) $updateData['description'] = $data->description;
        if ($data->category !== null) $updateData['category'] = $data->category;
        if ($data->price !== null) $updateData['price'] = $data->price;
        if ($data->discount_percentage !== null) $updateData['discount_percentage'] = $data->discount_percentage;
        if ($data->rating !== null) $updateData['rating'] = $data->rating;
        if ($data->stock !== null) $updateData['stock'] = $data->stock;
        
        $product->update($updateData);

        return $product;
    }
}
