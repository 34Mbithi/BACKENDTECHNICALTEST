<?php

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class);

test('product list is paginated', function () {
    $user = User::factory()->admin()->create();
    Product::factory(25)->create(['created_by' => $user->id]);

    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', "Bearer $token")
        ->getJson('/api/v1/products');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'category',
                    'price',
                    'stock',
                ],
            ],
            'meta' => [
                'current_page',
                'per_page',
                'total',
                'last_page',
            ],
        ])
        ->assertJsonPath('meta.current_page', 1)
        ->assertJsonPath('meta.per_page', 15);
});

test('filter by category works', function () {
    $user = User::factory()->admin()->create();
    Product::factory(5)->create(['created_by' => $user->id, 'category' => 'beauty']);
    Product::factory(5)->create(['created_by' => $user->id, 'category' => 'electronics']);

    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', "Bearer $token")
        ->getJson('/api/v1/products?filter[category]=beauty');

    $response->assertStatus(200)
        ->assertJsonPath('meta.total', 5);

    $data = $response->json('data');
    foreach ($data as $product) {
        expect($product['category'])->toBe('beauty');
    }
});

test('search works', function () {
    $user = User::factory()->admin()->create();
    Product::factory()->create([
        'created_by' => $user->id,
        'title' => 'Mascara Lash Princess',
        'description' => 'Perfect mascara',
    ]);
    Product::factory()->create([
        'created_by' => $user->id,
        'title' => 'Random Product',
        'description' => 'Something else',
    ]);

    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', "Bearer $token")
        ->getJson('/api/v1/products?filter[search]=mascara');

    $response->assertStatus(200)
        ->assertJsonPath('meta.total', 1)
        ->assertJsonPath('data.0.title', 'Mascara Lash Princess');
});

test('price filtering works', function () {
    $user = User::factory()->admin()->create();
    Product::factory()->create(['created_by' => $user->id, 'price' => 10]);
    Product::factory()->create(['created_by' => $user->id, 'price' => 50]);
    Product::factory()->create(['created_by' => $user->id, 'price' => 200]);

    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', "Bearer $token")
        ->getJson('/api/v1/products?filter[price_min]=20&filter[price_max]=100');

    $response->assertStatus(200)
        ->assertJsonPath('meta.total', 1)
        ->assertJsonPath('data.0.price', '50.00');
});

test('sorting works', function () {
    $user = User::factory()->admin()->create();
    Product::factory()->create(['created_by' => $user->id, 'title' => 'Zebra', 'price' => 100]);
    Product::factory()->create(['created_by' => $user->id, 'title' => 'Apple', 'price' => 50]);
    Product::factory()->create(['created_by' => $user->id, 'title' => 'Banana', 'price' => 75]);

    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', "Bearer $token")
        ->getJson('/api/v1/products?sort=title');

    $response->assertStatus(200);

    $data = $response->json('data');
    expect($data[0]['title'])->toBe('Apple');
    expect($data[1]['title'])->toBe('Banana');
    expect($data[2]['title'])->toBe('Zebra');
});

test('admin can create product', function () {
    $admin = User::factory()->admin()->create();
    $token = $admin->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', "Bearer $token")
        ->postJson('/api/v1/products', [
            'title' => 'New Product',
            'description' => 'Description',
            'category' => 'beauty',
            'price' => 29.99,
            'stock' => 50,
        ]);

    $response->assertStatus(201)
        ->assertJsonPath('data.title', 'New Product')
        ->assertJsonPath('data.created_by', null); // Not included in resource by default

    $this->assertDatabaseHas('products', [
        'title' => 'New Product',
        'created_by' => $admin->id,
    ]);
});

test('normal user cannot create product', function () {
    $user = User::factory()->create(['role' => 'user']);
    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', "Bearer $token")
        ->postJson('/api/v1/products', [
            'title' => 'New Product',
            'category' => 'beauty',
            'price' => 29.99,
            'stock' => 50,
        ]);

    $response->assertStatus(403);
});

test('can get single product', function () {
    $user = User::factory()->admin()->create();
    $product = Product::factory()->create(['created_by' => $user->id]);

    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', "Bearer $token")
        ->getJson("/api/v1/products/{$product->id}");

    $response->assertStatus(200)
        ->assertJsonPath('data.id', $product->id)
        ->assertJsonPath('data.title', $product->title);
});

test('admin can update product', function () {
    $admin = User::factory()->admin()->create();
    $product = Product::factory()->create(['created_by' => $admin->id]);

    $token = $admin->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', "Bearer $token")
        ->patchJson("/api/v1/products/{$product->id}", [
            'title' => 'Updated Title',
            'price' => 99.99,
        ]);

    $response->assertStatus(200)
        ->assertJsonPath('data.title', 'Updated Title')
        ->assertJsonPath('data.price', '99.99');

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'title' => 'Updated Title',
    ]);
});

test('creator can update their product', function () {
    $creator = User::factory()->create(['role' => 'user']);
    $product = Product::factory()->create(['created_by' => $creator->id]);

    $token = $creator->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', "Bearer $token")
        ->patchJson("/api/v1/products/{$product->id}", [
            'title' => 'Updated by Creator',
        ]);

    $response->assertStatus(200)
        ->assertJsonPath('data.title', 'Updated by Creator');
});

test('other user cannot update product', function () {
    $creator = User::factory()->create();
    $otherUser = User::factory()->create();
    $product = Product::factory()->create(['created_by' => $creator->id]);

    $token = $otherUser->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', "Bearer $token")
        ->patchJson("/api/v1/products/{$product->id}", [
            'title' => 'Hacked Title',
        ]);

    $response->assertStatus(403);
});

test('admin can delete product', function () {
    $admin = User::factory()->admin()->create();
    $product = Product::factory()->create(['created_by' => $admin->id]);

    $token = $admin->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', "Bearer $token")
        ->deleteJson("/api/v1/products/{$product->id}");

    $response->assertStatus(204);

    $this->assertSoftDeleted('products', ['id' => $product->id]);
});

test('non-admin cannot delete product', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['created_by' => $user->id]);

    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', "Bearer $token")
        ->deleteJson("/api/v1/products/{$product->id}");

    $response->assertStatus(403);
});

test('upload thumbnail stores file and returns URL', function () {
    Storage::fake('public');

    $admin = User::factory()->admin()->create();
    $product = Product::factory()->create(['created_by' => $admin->id]);

    $token = $admin->createToken('test-token')->plainTextToken;

    // Use a text file as placeholder since image generation requires GD library
    $file = UploadedFile::fake()->create('thumbnail.jpg', 100, 'image/jpeg');

    $response = $this->withHeader('Authorization', "Bearer $token")
        ->postJson("/api/v1/products/{$product->id}/thumbnail", [
            'thumbnail' => $file,
        ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'thumbnail',
            ],
        ]);

    $thumbnail = $response->json('data.thumbnail');
    expect($thumbnail)->toContain('storage/products');

    Storage::disk('public')->assertExists("products/{$product->id}/thumbnail.jpg");
});

test('thumbnail upload validates file type', function () {
    Storage::fake('public');

    $admin = User::factory()->admin()->create();
    $product = Product::factory()->create(['created_by' => $admin->id]);

    $token = $admin->createToken('test-token')->plainTextToken;

    $file = UploadedFile::fake()->create('not-an-image.txt', 100);

    $response = $this->withHeader('Authorization', "Bearer $token")
        ->postJson("/api/v1/products/{$product->id}/thumbnail", [
            'thumbnail' => $file,
        ]);

    $response->assertStatus(422);
});

test('unauthorized requests fail with 401', function () {
    $response = $this->getJson('/api/v1/products');

    $response->assertStatus(401);
});

test('includes creator when requested', function () {
    $creator = User::factory()->create();
    $product = Product::factory()->create(['created_by' => $creator->id]);

    $user = User::factory()->create();
    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', "Bearer $token")
        ->getJson('/api/v1/products?include=creator');

    $response->assertStatus(200);

    $data = $response->json('data.0');
    expect($data['creator']['id'])->toBe($creator->id);
    expect($data['creator']['name'])->toBe($creator->name);
});
