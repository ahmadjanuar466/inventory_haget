<?php

namespace App\Services\Products;

use App\Models\Products;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

class ProductImpl implements ProductServices
{
    public function createProducts(array $attributes): Products
    {
        return Products::create($this->preparePayload($attributes));
    }
    public function updateProducts(Products $product, array $attributes): Products
    {
        $product->update($this->preparePayload($attributes));
        return $product->refresh();
    }
    public function deleteProducts(Products $product): bool
    {
        if ($product->stocks()->exists() || $product->productsUnits()->exists() || $product->productMovements()->exists() || $product->purchaseReceiptItems()->exists()) {
            return false;
        }
        return (bool) $product->delete();
    }

    public function getProductsById(int $id): Products
    {
        return Products::query()
            ->with(['units', 'categories'])
            ->findOrFail($id);
    }

    public function paginateProducts(string $search = '', int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        $categoryId = $filters['category_id'] ?? '';
        $unitId = $filters['units_id'] ?? '';

        $query = Products::query()
            ->with(['units', 'categories'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('cost_price', 'like', "%{$search}%")
                        ->orWhere('sell_price', 'like', "%{$search}%");
                });
            })
            ->when($categoryId !== '', function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->when($unitId !== '', function ($query) use ($unitId) {
                $query->where('units_id', $unitId);
            })
            ->orderBy('name')
            ->paginate($perPage);

        return $query;
    }

    protected function preparePayload(array $attributes): array
    {
        $payload = Arr::only($attributes, [
            'sku',
            'name',
            'category_id',
            'units_id',
            'track_stock',
            'has_expiry',
            'cost_price',
            'sell_price',
            'min_stock',
            'is_active'
        ]);

        foreach (['cost_price', 'sell_price', 'min_stock'] as $nullableDecimal) {
            if (($payload[$nullableDecimal] ?? '') === '') {
                $payload[$nullableDecimal] = null;
            }
        }

        foreach (['track_stock', 'has_expiry', 'is_active'] as $flag) {
            if (array_key_exists($flag, $payload)) {
                $payload[$flag] = (int) $payload[$flag];
            }
        }

        return $payload;
    }
}
