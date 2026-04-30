<?php

namespace App\Services\Products;

use App\Models\Products;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ProductImpl implements ProductServices
{
    public function __construct(
        protected ProductUnitServices $productUnitServices,
    ) {}

    public function createProduct(array $attributes): Products
    {
        [$payload, $productUnits] = $this->separateProductUnits($attributes);

        return DB::transaction(function () use ($payload, $productUnits) {
            $product = Products::create($payload);

            $this->productUnitServices->syncProductUnits(
                $product,
                (int) $payload['units_id'],
                $productUnits,
            );

            return $product->load('categories', 'units', 'productUnits.unit');
        });
    }
    public function updateProduct(Products $product, array $attributes): Products
    {
        [$payload, $productUnits] = $this->separateProductUnits($attributes);

        return DB::transaction(function () use ($product, $payload, $productUnits) {
            $product->update($payload);

            $this->productUnitServices->syncProductUnits(
                $product,
                (int) $payload['units_id'],
                $productUnits,
            );

            return $product->refresh()->load('categories', 'units', 'productUnits.unit');
        });
    }
    public function deleteProduct(Products $product): bool
    {
        if (
            $product->stocks()->exists() ||
            $product->stockMovements()->exists()
        ) {
            return false;
        }
        return (bool) $product->delete();
    }
    public function getProductById(int $id): Products
    {
        return Products::query()->with('categories', 'units', 'productUnits.unit')->findOrFail($id);
    }
    public function paginateProducts(string $search = '', int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        $query = Products::query()
            ->with('categories', 'units', 'productUnits.unit')
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($subQuery) use ($search) {
                    $subQuery->where('sku', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                });
            })
            ->when(isset($filters['category_id']) && $filters['category_id'] !== '', function ($q) use ($filters) {
                $q->where('category_id', $filters['category_id']);
            })
            ->when(isset($filters['units_id']) && $filters['units_id'] !== '', function ($q) use ($filters) {
                $q->where('units_id', $filters['units_id']);
            })
            ->when(isset($filters['is_active']) && $filters['is_active'] !== '', function ($q) use ($filters) {
                $q->where('is_active', $filters['is_active']);
            });
        return $query->paginate($perPage);
    }

    protected function separateProductUnits(array $attributes): array
    {
        return [
            $this->preparePayload($attributes),
            Arr::get($attributes, 'product_units', []),
        ];
    }

    protected function preparePayload(array $attributes): array
    {
        return Arr::only($attributes, [
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
    }
}
