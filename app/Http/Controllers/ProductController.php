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
        // Get the validated filter parameters
        $validated = $request->validated(); // Get all validated data first
        $filters = $validated['filter'] ?? [];
        $perPage = $validated['per_page'] ?? 15;

        // Start the query with eager loading of the category relationship
        $query = Product::with('category');

        // Apply filters using conditional clauses for cleaner code
        $query->when(isset($filters['name']), function ($q) use ($filters) {
            $q->filterByName($filters['name']);
        });

        $query->when(isset($filters['category_id']), function ($q) use ($filters) {
            $q->filterByCategory($filters['category_id']);
        });

        $query->when(isset($filters['status']), function ($q) use ($filters) {
            $q->filterByStatus($filters['status']);
        });

        // Execute the query with pagination, preserving query string parameters
        $products = $query->paginate($perPage)->withQueryString();

        // Note: $products->appends(['filters' => $filters]); is redundant if using withQueryString()
        // and the filters are already part of the request's query string.
        // withQueryString() handles appending all current query string parameters.

        // If it's an API request, return JSON
        if ($request->expectsJson()) {
            return new ProductCollection($products);
        }

        // Fetch categories for the filter dropdown (consider caching)
        $categories = Category::query()
            ->select('id', 'name')
            ->orderBy('name') // Good practice to order dropdowns
            ->get();

        // If it's a web request via Inertia, return the view
        return Inertia::render('Products/Index', [
            // Use the resource collection for consistent data structure
            'products' => ProductResource::collection($products),
            // Pass the current filters to populate the filter form state
            'filters' => $filters,
            // Pass categories for the filter dropdown
            'categories' => $categories->toArray(),
        ]);
    }
}
