<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Products\CreateProductAction;
use App\Actions\Products\DeleteProductAction;
use App\Actions\Products\UpdateProductAction;
use App\Actions\Products\UploadThumbnailAction;
use App\Data\ProductData;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

class ProductController
{
    use AuthorizesRequests;

    /**
     * Get paginated list of products.
     */
    public function index(Request $request)
    {
        $query = QueryBuilder::for(Product::class)
            ->allowedFields(['id', 'title', 'description', 'category', 'price', 'discount_percentage', 'rating', 'stock', 'thumbnail_path', 'created_at', 'updated_at', 'created_by'])
            ->allowedFilters([
                AllowedFilter::exact('category'),
                AllowedFilter::callback('price_min', function ($query, $value) {
                    $query->where('price', '>=', $value);
                }),
                AllowedFilter::callback('price_max', function ($query, $value) {
                    $query->where('price', '<=', $value);
                }),
                AllowedFilter::callback('search', function ($query, $value) {
                    $query->where('title', 'like', "%{$value}%")
                          ->orWhere('description', 'like', "%{$value}%");
                }),
            ])
            ->allowedSorts(['price', 'stock', 'title', 'rating', 'created_at'])
            ->allowedIncludes('creator')
            ->defaultSort('created_at')
            ->paginate(
                perPage: min($request->query('per_page', 15), 100),
                columns: ['*'],
                pageName: 'page',
                page: $request->query('page', 1)
            );

        return response()->json([
            'data' => ProductResource::collection($query->items()),
            'meta' => [
                'current_page' => $query->currentPage(),
                'per_page' => $query->perPage(),
                'total' => $query->total(),
                'last_page' => $query->lastPage(),
            ],
        ], Response::HTTP_OK);
    }

    /**
     * Get single product by ID.
     */
    public function show(Product $product)
    {
        $product->load('creator');

        return response()->json([
            'data' => new ProductResource($product),
        ], Response::HTTP_OK);
    }

    /**
     * Create a new product.
     */
    public function store(Request $request, CreateProductAction $action)
    {
        $this->authorize('create', Product::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'rating' => 'nullable|numeric|min:0|max:5',
            'stock' => 'required|integer|min:0',
        ]);

        $data = ProductData::from($validated);
        $product = $action->execute($data, $request->user());

        return response()->json([
            'data' => new ProductResource($product),
        ], Response::HTTP_CREATED);
    }

    /**
     * Update a product.
     */
    public function update(Request $request, Product $product, UpdateProductAction $action)
    {
        $this->authorize('update', $product);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'sometimes|required|string|max:100',
            'price' => 'sometimes|required|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'rating' => 'nullable|numeric|min:0|max:5',
            'stock' => 'sometimes|required|integer|min:0',
        ]);

        $data = ProductData::from($validated);
        $product = $action->execute($product, $data);

        return response()->json([
            'data' => new ProductResource($product),
        ], Response::HTTP_OK);
    }

    /**
     * Delete a product.
     */
    public function destroy(Product $product, DeleteProductAction $action)
    {
        $this->authorize('delete', $product);

        $action->execute($product);

        return response()->json([], Response::HTTP_NO_CONTENT);
    }

    /**
     * Upload product thumbnail.
     */
    public function uploadThumbnail(Request $request, Product $product, UploadThumbnailAction $action)
    {
        $this->authorize('uploadThumbnail', $product);

        $request->validate([
            'thumbnail' => 'required|image|mimes:jpeg,png,webp|max:2048',
        ]);

        $product = $action->execute($product, $request->file('thumbnail'));

        return response()->json([
            'data' => new ProductResource($product),
        ], Response::HTTP_OK);
    }
}
