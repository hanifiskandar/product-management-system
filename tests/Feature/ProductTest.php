<?php

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// --- Index ---

test('products index returns successful response', function () {
    $this->get('/products')->assertOk();
});

test('products index lists products', function () {
    $product = Product::factory()->create(['name' => 'Test Widget']);

    $this->get('/products')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Products/Index')
            ->has('products.data', 1)
            ->where('products.data.0.name', 'Test Widget')
        );
});

test('products index excludes soft deleted products', function () {
    Product::factory()->create(['name' => 'Active Product']);
    $deleted = Product::factory()->create(['name' => 'Deleted Product']);
    $deleted->delete();

    $this->get('/products')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('products.total', 1)
            ->where('products.data.0.name', 'Active Product')
        );
});

test('products index filters by search', function () {
    Product::factory()->create(['name' => 'Laptop Pro']);
    Product::factory()->create(['name' => 'Desk Chair']);

    $this->get('/products?search=Laptop')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('products.total', 1)
            ->where('products.data.0.name', 'Laptop Pro')
        );
});

test('products index filters by category', function () {
    $electronics = Category::factory()->create(['name' => 'Electronics']);
    $furniture = Category::factory()->create(['name' => 'Furniture']);

    Product::factory()->create(['name' => 'Monitor', 'category_id' => $electronics->id]);
    Product::factory()->create(['name' => 'Sofa', 'category_id' => $furniture->id]);

    $this->get("/products?category_id={$electronics->id}")
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('products.total', 1)
            ->where('products.data.0.name', 'Monitor')
        );
});

test('products index passes stats to view', function () {
    $category = Category::factory()->create();
    Product::factory()->count(3)->create(['quantity' => 20, 'category_id' => $category->id]);
    Product::factory()->count(2)->create(['quantity' => 2, 'category_id' => $category->id]);

    $this->get('/products')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('total_products', 5)
            ->where('low_stock_count', 2)
        );
});

// --- Create ---

test('products create page returns successful response', function () {
    $this->get('/products/create')->assertOk();
});

test('products create page passes categories to view', function () {
    Category::factory()->count(3)->create();

    $this->get('/products/create')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Products/Create')
            ->has('categories', 3)
        );
});

// --- Store ---

test('can create a product', function () {
    $category = Category::factory()->create();

    $this->post('/products', [
        'name' => 'New Product',
        'quantity' => 10,
        'category_id' => $category->id,
    ])->assertRedirect('/products');

    $this->assertDatabaseHas('products', [
        'name' => 'New Product',
        'quantity' => 10,
        'category_id' => $category->id,
    ]);
});

test('store validates name is required', function () {
    $category = Category::factory()->create();

    $this->post('/products', [
        'name' => '',
        'quantity' => 10,
        'category_id' => $category->id,
    ])->assertSessionHasErrors('name');
});

test('store validates quantity is required', function () {
    $category = Category::factory()->create();

    $this->post('/products', [
        'name' => 'Product',
        'quantity' => '',
        'category_id' => $category->id,
    ])->assertSessionHasErrors('quantity');
});

test('store validates quantity must be at least 1', function () {
    $category = Category::factory()->create();

    $this->post('/products', [
        'name' => 'Product',
        'quantity' => 0,
        'category_id' => $category->id,
    ])->assertSessionHasErrors('quantity');
});

test('store validates category is required', function () {
    $this->post('/products', [
        'name' => 'Product',
        'quantity' => 10,
        'category_id' => '',
    ])->assertSessionHasErrors('category_id');
});

test('store validates category must exist', function () {
    $this->post('/products', [
        'name' => 'Product',
        'quantity' => 10,
        'category_id' => 9999,
    ])->assertSessionHasErrors('category_id');
});

// --- Edit ---

test('products edit page returns successful response', function () {
    $product = Product::factory()->create();

    $this->get("/products/{$product->id}/edit")->assertOk();
});

test('products edit page passes product and categories to view', function () {
    $product = Product::factory()->create(['name' => 'Existing Product']);
    Category::factory()->count(2)->create();

    $this->get("/products/{$product->id}/edit")
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Products/Edit')
            ->where('product.name', 'Existing Product')
            ->has('categories')
        );
});

// --- Update ---

test('can update a product', function () {
    $category = Category::factory()->create();
    $product = Product::factory()->create(['name' => 'Old Name', 'category_id' => $category->id]);

    $this->put("/products/{$product->id}", [
        'name' => 'Updated Name',
        'quantity' => 25,
        'category_id' => $category->id,
    ])->assertRedirect('/products');

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'name' => 'Updated Name',
        'quantity' => 25,
    ]);
});

test('update validates name is required', function () {
    $product = Product::factory()->create();

    $this->put("/products/{$product->id}", [
        'name' => '',
        'quantity' => 10,
        'category_id' => $product->category_id,
    ])->assertSessionHasErrors('name');
});

test('update validates quantity must be at least 1', function () {
    $product = Product::factory()->create();

    $this->put("/products/{$product->id}", [
        'name' => 'Product',
        'quantity' => 0,
        'category_id' => $product->category_id,
    ])->assertSessionHasErrors('quantity');
});

// --- Delete ---

test('can soft delete a product', function () {
    $product = Product::factory()->create();

    $this->delete("/products/{$product->id}")->assertRedirect();

    $this->assertSoftDeleted('products', ['id' => $product->id]);
});

test('soft deleted product is excluded from index', function () {
    $product = Product::factory()->create();
    $product->delete();

    $this->get('/products')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('products.total', 0)
        );
});
