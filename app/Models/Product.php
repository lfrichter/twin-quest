<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'category_id',
        'status',
    ];

    /**
     * Get the category of the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeFilterByName(Builder $query, string $name): void
    {
        $query->where('name', 'like', '%'.$name.'%');
    }

    public function scopeFilterByCategory(Builder $query, int|string $categoryId): void
    {
        $query->where('category_id', $categoryId);
    }

    public function scopeFilterByStatus(Builder $query, string|int $status): void
    {
        $query->where('status', $status);
    }
}
