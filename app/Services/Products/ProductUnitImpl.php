<?php

namespace App\Services\Products;

use App\Models\Products;
use Illuminate\Support\Arr;

class ProductUnitImpl implements ProductUnitServices
{
    public function syncProductUnits(Products $product, int $baseUnitId, array $attributes = []): void
    {
        $conversion = collect($attributes)->first();
        $stockUnitId = (int) Arr::get((array) $conversion, 'unit_id', $baseUnitId);

        $payloads = [[
            'unit_id' => $stockUnitId,
            'conversion_qty' => $this->resolveConversionQty((array) $conversion),
            'is_active' => (int) Arr::get((array) $conversion, 'is_active', 1),
            'is_base' => $stockUnitId === $baseUnitId ? 1 : 0,
        ]];

        $product->productUnits()->delete();
        $product->productUnits()->createMany($payloads);
    }

    protected function normalizeConversionQty(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $normalized = trim((string) $value);

        if ($normalized === '') {
            return null;
        }

        return str_replace(',', '.', $normalized);
    }

    protected function resolveConversionQty(array $item): string
    {
        $conversionQty = $this->normalizeConversionQty(Arr::get($item, 'conversion_qty'));

        return $conversionQty === null ? '1' : $conversionQty;
    }
}
