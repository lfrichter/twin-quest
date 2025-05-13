<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductFilterRequest;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class ProductController extends Controller
{
    /**
     * Display a paginated and filterable list of products.
     *
     * @param  ProductFilterRequest  $request  The request object containing validated filter data.
     * @return ProductCollection|InertiaResponse Returns JSON resource collection for API requests or Inertia response for web requests.
     */
    public function index(ProductFilterRequest $request): ProductCollection|InertiaResponse
    {
        $query = Product::query()->with('category');

        // Get the validated filter parameters
        $validated = $request->validated(); // Get all validated data first

        // / Filter by name if 'name' is provided and not null
        $query->when($validated['name'] ?? null, function ($q, $name) {
            // Apply the name filter using the scope defined in the Product model
            $q->filterByName($name);
        });

        // Filter by category_id if 'category_id' is provided and not null
        $query->when($validated['category_id'] ?? null, function ($q, $categoryId) {
            // Apply the category filter using the scope
            $q->filterByCategory($categoryId);
        });

        // Filter by status if 'status' is provided and not null
        $query->when($validated['status'] ?? null, function ($q, $status) {
            // Apply the status filter using the scope
            $q->filterByStatus($status);
        });

        // Determine items per page from validated data or default to 15
        $perPage = $validated['per_page'] ?? 15;

        // Paginate the results and ensure filter parameters are appended to pagination links
        $products = $query->paginate($perPage)->withQueryString();

        // If it's an API request, return JSON
        if ($request->expectsJson()) {
            return new ProductCollection($products);
        }

        // Fetch categories for the filter dropdown (consider caching)
        $categories = Category::query()
            ->select('id', 'name')
            ->orderBy('name') // Good practice to order dropdowns
            ->get();

        $activeFilters = [
            'name' => $validated['name'] ?? null,
            'category_id' => $validated['category_id'] ?? null,
            'status' => $validated['status'] ?? null,
        ];

        return Inertia::render('Products/Index', [
            'products' => ProductResource::collection($products),
            'filters' => $activeFilters,
            'categories' => $categories->toArray(),
        ]);
    }
}
