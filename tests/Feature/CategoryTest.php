<?php

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// --- Index ---

test('categories index returns successful response', function () {
    $this->get('/categories')->assertOk();
});

test('categories index lists categories', function () {
    Category::factory()->create(['name' => 'Electronics']);

    $this->get('/categories')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Categories/Index')
            ->has('categories.data', 1)
            ->where('categories.data.0.name', 'Electronics')
        );
});

test('categories index excludes soft deleted categories', function () {
    Category::factory()->create(['name' => 'Active']);
    $deleted = Category::factory()->create(['name' => 'Deleted']);
    $deleted->delete();

    $this->get('/categories')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('categories.total', 1)
            ->where('categories.data.0.name', 'Active')
        );
});

test('categories index filters by search', function () {
    Category::factory()->create(['name' => 'Electronics']);
    Category::factory()->create(['name' => 'Furniture']);

    $this->get('/categories?search=Elec')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('categories.total', 1)
            ->where('categories.data.0.name', 'Electronics')
        );
});

test('categories index passes products count', function () {
    $category = Category::factory()->create();
    Product::factory()->count(3)->create(['category_id' => $category->id]);

    $this->get('/categories')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('categories.data.0.products_count', 3)
        );
});

test('categories index passes total categories count', function () {
    Category::factory()->count(4)->create();

    $this->get('/categories')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('total_categories', 4)
        );
});

// --- Store ---

test('can create a category', function () {
    $this->post('/categories', ['name' => 'New Category'])
        ->assertRedirect('/categories');

    $this->assertDatabaseHas('categories', ['name' => 'New Category']);
});

test('store validates name is required', function () {
    $this->post('/categories', ['name' => ''])
        ->assertSessionHasErrors('name');
});

test('store validates name must be unique', function () {
    Category::factory()->create(['name' => 'Electronics']);

    $this->post('/categories', ['name' => 'Electronics'])
        ->assertSessionHasErrors('name');
});

// --- Update ---

test('can update a category', function () {
    $category = Category::factory()->create(['name' => 'Old Name']);

    $this->put("/categories/{$category->id}", ['name' => 'New Name'])
        ->assertRedirect('/categories');

    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'name' => 'New Name',
    ]);
});

test('update validates name is required', function () {
    $category = Category::factory()->create();

    $this->put("/categories/{$category->id}", ['name' => ''])
        ->assertSessionHasErrors('name');
});

test('update validates name must be unique', function () {
    Category::factory()->create(['name' => 'Electronics']);
    $category = Category::factory()->create(['name' => 'Furniture']);

    $this->put("/categories/{$category->id}", ['name' => 'Electronics'])
        ->assertSessionHasErrors('name');
});

test('update allows keeping same name on edit', function () {
    $category = Category::factory()->create(['name' => 'Electronics']);

    $this->put("/categories/{$category->id}", ['name' => 'Electronics'])
        ->assertRedirect('/categories');

    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'name' => 'Electronics',
    ]);
});
