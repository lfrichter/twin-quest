<?php

namespace App\Services;

use App\Http\Requests\ProductFilterRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Cache;

class ProductService
{
    private const PRODUCT_CACHE_DURATION = 600; // 10 minutes

    private const CATEGORY_CACHE_DURATION = 3600; // 60 minutes

    /**
     * Get a paginated and filterable list of products, with caching.
     *
     * @param  ProductFilterRequest  $request  The request object containing validated filter data.
     * @return LengthAwarePaginator Returns a paginated collection of products.
     */
    public function getFilteredAndPaginatedProducts(ProductFilterRequest $request): LengthAwarePaginator
    {
        $validated = $request->validated();
        $perPage = $validated['per_page'] ?? 15;

        $cacheKeyParts = $request->query(); // Includes page, per_page, and all filters
        ksort($cacheKeyParts);
        $cacheKey = 'products_list_'.md5(http_build_query($cacheKeyParts));

        return Cache::remember($cacheKey, self::PRODUCT_CACHE_DURATION, function () use ($validated, $perPage) {
            $query = Product::query()->with('category');

            $query->when($validated['name'] ?? null, function ($q, $name) {
                $q->filterByName($name);
            });

            $query->when($validated['category_id'] ?? null, function ($q, $categoryId) {
                $q->filterByCategory($categoryId);
            });

            $query->when($validated['status'] ?? null, function ($q, $status) {
                $q->filterByStatus($status);
            });

            return $query->paginate($perPage)->withQueryString();
        });
    }

    /**
     * Get the list of categories for filtering, with caching.
     *
     * @return EloquentCollection Returns a collection of categories.
     */
    public function getCategoriesForFilter(): EloquentCollection
    {
        $cacheKey = 'categories_dropdown_list';

        return Cache::remember($cacheKey, self::CATEGORY_CACHE_DURATION, function () {
            return Category::query()
                ->select('id', 'name')
                ->orderBy('name')
                ->get();
        });
    }
}
