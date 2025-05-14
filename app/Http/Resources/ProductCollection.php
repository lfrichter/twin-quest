<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

// use Illuminate\Support\Collection; // Opcional: importar para type hint

class ProductCollection extends ResourceCollection
{
    /**
     * The resource that this resource collection collects.
     *
     * @var string
     */
    public $collects = \App\Http\Resources\ProductResource::class;

    /**
     * Defines the keys that are considered filterable from the request query.
     *
     * @var array<int, string>
     */
    protected const FILTERABLE_KEYS = [
        'name',
        'category_id',
        'status',
    ];

    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @return array<string, mixed>
     */
    public function with(Request $request): array
    {
        $appliedFilters = collect(self::FILTERABLE_KEYS)
            ->filter(fn (string $key): bool => $request->has($key)) // Keep only keys present in the request
            ->mapWithKeys(function (string $key) use ($request): array { // Maps to key => value pairs
                return [$key => $request->query($key)];
            })
            ->filter(fn ($value): bool => $value !== null) // Remove filters where the value is null
            ->all();

        return [
            'meta' => [
                'filters' => $appliedFilters,
            ],
        ];
    }
}
