<?php

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

// This tells Pest to use the RefreshDatabase trait for all tests in this file.
uses(RefreshDatabase::class);

/**
 * Test suite for the Product API endpoint.
 * It covers listing, pagination, filtering, and error handling as per STORY-001
 * and the technical requirements document.
 */

/**
 * Test if products can be listed with pagination.
 * It also checks if the 'meta.filters' is an empty array when no filters are applied.
 */
test('can list products with pagination and correct meta filters when no filters applied', function () {
    // Arrange: Create a category and 20 products associated with it.
    $category = Category::factory()->create();
    Product::factory(20)->create(['category_id' => $category->id]);

    // Act: Make a GET request to the /api/products endpoint.
    $response = $this->getJson('/api/products');

    // Assert: Check for a 200 OK status and the correct JSON structure.
    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'description',
                    'price',
                    'status',
                    'category' => ['id', 'name'],
                    'created_at',
                ],
            ],
            'links' => ['first', 'last', 'prev', 'next'],
            'meta' => [
                'current_page',
                'from',
                'last_page',
                'links',
                'path',
                'per_page',
                'to',
                'total',
                'filters', // As per requirements, filters applied should be in meta.
            ],
        ])
        ->assertJsonCount(15, 'data') // Default per_page is 15.
        ->assertJsonPath('meta.total', 20)
        ->assertJsonPath('meta.filters', []); // No filters applied, so 'filters' should be an empty array.
});

/**
 * Test if products can be filtered by name.
 * Uses 'filter[name]' query parameter as per requirements.
 */
test('can filter products by name', function () {
    // Arrange: Create a category and two products with different names.
    $category = Category::factory()->create();
    Product::factory()->create([
        'name' => 'Laptop Pro X',
        'category_id' => $category->id,
    ]);
    Product::factory()->create([
        'name' => 'Desktop Pro Series',
        'category_id' => $category->id,
    ]);

    $filterName = 'Laptop';
    // Act: Make a GET request with the name filter.
    $response = $this->getJson("/api/products?name={$filterName}");

    // Assert: Check for a 200 OK status, 1 item in data, and correct name and meta.filters.
    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.name', 'Laptop Pro X')
        ->assertJsonPath('meta.filters.name', $filterName);
});

/**
 * Test if products can be filtered by category.
 * Uses 'filter[category_id]' query parameter as per requirements.
 */
test('can filter products by category', function () {
    // Arrange: Create two categories and products associated with each.
    $category1 = Category::factory()->create();
    $category2 = Category::factory()->create();
    Product::factory(3)->create(['category_id' => $category1->id]);
    Product::factory(2)->create(['category_id' => $category2->id]);

    // Act: Make a GET request with the category_id filter.
    $response = $this->getJson("/api/products?category_id={$category1->id}");

    // Assert: Check for a 200 OK status, 3 items in data, and correct meta.filters.
    $response->assertStatus(200)
        ->assertJsonCount(3, 'data')
        ->assertJsonPath('meta.filters.category_id', (string) $category1->id); // Query params are strings.
});

/**
 * Test if products can be filtered by status.
 * Uses 'filter[status]' query parameter as per requirements.
 */
test('can filter products by status', function () {
    // Arrange: Create a category and products with different statuses.
    $category = Category::factory()->create();
    Product::factory(2)->create([
        'status' => 'active',
        'category_id' => $category->id,
    ]);
    Product::factory()->create([
        'status' => 'inactive',
        'category_id' => $category->id,
    ]);

    $filterStatus = 'active';
    // Act: Make a GET request with the status filter.
    $response = $this->getJson("/api/products?status={$filterStatus}");

    // Assert: Check for a 200 OK status, 2 items in data, and correct status and meta.filters.
    $response->assertStatus(200)
        ->assertJsonCount(2, 'data')
        ->assertJsonPath('data.0.status', $filterStatus)
        ->assertJsonPath('meta.filters.status', $filterStatus);
});

/**
 * Test if per_page pagination can be customized.
 */
test('can customize per_page pagination', function () {
    // Arrange: Create a category and 30 products.
    $category = Category::factory()->create();
    Product::factory(30)->create([
        'category_id' => $category->id,
    ]);

    // Act: Make a GET request with per_page parameter.
    $response = $this->getJson('/api/products?per_page=20');

    // Assert: Check for a 200 OK status, 20 items in data, and correct meta.per_page.
    $response->assertStatus(200)
        ->assertJsonCount(20, 'data')
        ->assertJsonPath('meta.per_page', 20);
});

/**
 * Test if API returns empty data when no products match filters.
 */
test('returns empty data when no products match filters', function () {
    // Arrange: Create a category and one product.
    $category = Category::factory()->create();
    Product::factory()->create([
        'name' => 'Laptop Pro',
        'category_id' => $category->id,
    ]);

    $filterName = 'NonExistentProduct';
    // Act: Make a GET request with a name filter that matches no products.
    $response = $this->getJson("/api/products?name={$filterName}");

    // Assert: Check for a 200 OK status, 0 items in data, and correct meta.total and meta.filters.
    $response->assertStatus(200)
        ->assertJsonCount(0, 'data')
        ->assertJsonPath('meta.total', 0)
        ->assertJsonPath('meta.filters.name', $filterName);
});

/**
 * Test if API returns validation errors for invalid filter parameters as per specification.
 */
test('returns validation errors for invalid filter parameters as per spec', function () {
    $response = $this->getJson('/api/products?category_id=abc');
    $response->assertStatus(422)
        ->assertJsonValidationErrorFor('category_id')
        ->assertJsonFragment(['category_id' => ['The category id field must be an integer.']]);

    $response = $this->getJson('/api/products?status=non_existent_status');
    $response->assertStatus(422)
        ->assertJsonValidationErrorFor('status')
        ->assertJsonFragment(['status' => ['The selected status is invalid.']]);

    // Test invalid per_page (non-integer)
    $response = $this->getJson('/api/products?per_page=abc');
    $response->assertStatus(422)
        ->assertJsonValidationErrorFor('per_page'); // Expecting Laravel's default message for integer rule

    // Test invalid per_page (too large, assuming max:100 from ProductFilterRequest)
    $response = $this->getJson('/api/products?per_page=200');
    $response->assertStatus(422)
        ->assertJsonValidationErrorFor('per_page'); // Expecting Laravel's default message for max rule
});

/**
 * Test if all applied filters are correctly reflected in the 'meta.filters' part of the response.
 */
test('returns all applied filters in meta information', function () {
    // Arrange: Create a category and a product.
    $category = Category::factory()->create();
    Product::factory()->create([
        'name' => 'Test Product For Meta Filters',
        'category_id' => $category->id,
        'status' => 'active',
    ]);

    $filters = [
        'name' => 'Test Product',
        'category_id' => $category->id,
        'status' => 'active',
    ];

    // Act: Make a GET request with multiple filters.
    $response = $this->getJson('/api/products?'.http_build_query($filters));

    // dd(
    //     json_decode($response->getContent()),
    // );

    // Assert: Check for a 200 OK status and that all filters are present in meta.filters.
    $response->assertStatus(200)
        ->assertJsonPath('meta.filters.name', $filters['name'])
        ->assertJsonPath('meta.filters.category_id', (string) $filters['category_id'])
        ->assertJsonPath('meta.filters.status', $filters['status']);
});
