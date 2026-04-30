<?php

namespace App\Services\ProductPrice;

use App\Models\ProductPrice;
use App\Services\ProductPrices\ProductPriceServices;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ProductPriceImpl implements ProductPriceServices
{
    public function createProductPrice(array $attributes): ProductPrice
    {
        $payload = $this->preparePayload($attributes);

        return DB::transaction(function () use ($payload) {
            if ((bool) ($payload['is_active'] ?? false)) {
                $this->deactivateOtherProductPrices((int) $payload['product_id']);
            }

            return ProductPrice::create($payload)->load('product');
        });
    }
    public function updateProductPrice(ProductPrice $productPrice, array $attributes): ProductPrice
    {
        $payload = $this->preparePayload($attributes);

        return DB::transaction(function () use ($productPrice, $payload) {
            if ((bool) ($payload['is_active'] ?? false)) {
                $this->deactivateOtherProductPrices((int) $payload['product_id'], $productPrice->id);
            }

            $productPrice->update($payload);

            return $productPrice->refresh()->load('product');
        });
    }
    public function deleteProductPrice(ProductPrice $productPrice): bool
    {
        return (bool) $productPrice->delete();
    }
    public function getProductPriceById(int $id): ProductPrice
    {
        return ProductPrice::with('product')->findOrFail($id);
    }
    public function paginateProductPrices(string $search = '', int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        $query = ProductPrice::with('product')
            ->when($search, function ($q) use ($search) {
                $q->whereHas('product', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->when(isset($filters['product_id']) && $filters['product_id'] !== '', function ($q) use ($filters) {
                $q->where('product_id', $filters['product_id']);
            })
            ->when(isset($filters['is_active']) && $filters['is_active'] !== '', function ($q) use ($filters) {
                $q->where('is_active', $filters['is_active']);
            });
        return $query->orderByDesc('is_active')->orderByDesc('id')->paginate($perPage);
    }
    protected function preparePayload(array $attributes): array
    {
        return Arr::only($attributes, ['product_id', 'price', 'is_active']);
    }

    protected function deactivateOtherProductPrices(int $productId, ?int $exceptId = null): void
    {
        ProductPrice::query()
            ->where('product_id', $productId)
            ->when($exceptId !== null, function ($query) use ($exceptId) {
                $query->whereKeyNot($exceptId);
            })
            ->update(['is_active' => false]);
    }
}
