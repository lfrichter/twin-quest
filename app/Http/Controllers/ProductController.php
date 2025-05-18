<?php

declare(strict_types=1); // Add strict types declaration

namespace App\Http\Controllers;

use App\Http\Requests\ProductFilterRequest;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Services\ProductService;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class ProductController extends Controller
{
    protected ProductService $productService; // Add this property

    /**
     * Constructor to inject ProductService.
     */
    public function __construct(ProductService $productService) // Add constructor
    {
        $this->productService = $productService;
    }

    /**
     * Display a paginated and filterable list of products.
     * Uses ProductService to fetch and cache data.
     *
     * @param  ProductFilterRequest  $request  The request object containing validated filter data.
     * @return ProductCollection|InertiaResponse Returns JSON resource collection for API requests or Inertia response for web requests.
     */
    public function index(ProductFilterRequest $request): ProductCollection|InertiaResponse
    {
        $validated = $request->validated(); // Still needed for activeFilters

        $products = $this->productService->getFilteredAndPaginatedProducts($request);

        if ($request->expectsJson()) {
            return new ProductCollection($products);
        }

        $categories = $this->productService->getCategoriesForFilter();

        $activeFilters = [
            'name' => $validated['name'] ?? null,
            'category_id' => isset($validated['category_id']) ? (int) $validated['category_id'] : null,
            'status' => $validated['status'] ?? null,
            'per_page' => (int) ($validated['per_page'] ?? 15),
        ];

        return Inertia::render('Products/Index', [
            'products' => ProductResource::collection($products),
            'filters' => $activeFilters,
            'categories' => $categories->toArray(),
        ]);
    }
}
