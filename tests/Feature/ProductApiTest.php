<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_products_with_pagination(): void
    {
        // Arrange
        $category = Category::factory()->create();
        $products = Product::factory(20)->create(['category_id' => $category->id]);
        // Act
        $response = $this->getJson('/api/products');

        // Assert
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
                    'filters',
                ],
            ])
            ->assertJsonCount(15, 'data') // Default per_page is 15
            ->assertJsonPath('meta.total', 20);
    }

    public function test_can_filter_products_by_name(): void
    {
        // Arrange
        $category = Category::factory()->create();
        Product::factory()->create([
            'name' => 'Laptop Pro',
            'category_id' => $category->id,
        ]);
        Product::factory()->create([
            'name' => 'Desktop Pro',
            'category_id' => $category->id,
        ]);

        // Act
        $response = $this->getJson('/api/products?filter[name]=Laptop');

        // Assert
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Laptop Pro');
    }

    public function test_can_filter_products_by_category(): void
    {
        // Arrange
        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();
        Product::factory(3)->create(['category_id' => $category1->id]);
        Product::factory(2)->create(['category_id' => $category2->id]);

        // Act
        $response = $this->getJson("/api/products?filter[category_id]={$category1->id}");

        // Assert
        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_can_filter_products_by_status(): void
    {
        // Arrange
        $category = Category::factory()->create();
        Product::factory(2)->create([
            'status' => 'active',
            'category_id' => $category->id,
        ]);
        Product::factory()->create([
            'status' => 'inactive',
            'category_id' => $category->id,
        ]);

        // Act
        $response = $this->getJson('/api/products?filter[status]=active');

        // Assert
        $response->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.status', 'active');
    }

    public function test_can_customize_per_page_pagination(): void
    {
        // Arrange
        $category = Category::factory()->create();
        Product::factory(30)->create([
            'category_id' => $category->id,
        ]);

        // Act
        $response = $this->getJson('/api/products?per_page=20');

        // Assert
        $response->assertStatus(200)
            ->assertJsonCount(20, 'data')
            ->assertJsonPath('meta.per_page', 20);
    }

    public function test_returns_empty_data_when_no_products_match_filters(): void
    {
        // Arrange
        $category = Category::factory()->create();
        Product::factory()->create([
            'name' => 'Laptop Pro',
            'category_id' => $category->id,
        ]);

        // Act
        $response = $this->getJson('/api/products?filter[name]=NonExistent');

        // Assert
        $response->assertStatus(200)
            ->assertJsonCount(0, 'data')
            ->assertJsonPath('meta.total', 0);
    }
}
