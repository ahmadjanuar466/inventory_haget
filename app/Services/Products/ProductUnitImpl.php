<?php

namespace App\Services\Products;

use App\Models\Products;
use Illuminate\Support\Arr;

class ProductUnitImpl implements ProductUnitServices
{
    public function syncProductUnits(Products $product, int $baseUnitId, array $attributes = []): void
    {
        $payloads = collect($attributes)
            ->map(function ($item) {
                return [
                    'unit_id' => (int) Arr::get($item, 'unit_id', 0),
                    'conversion_qty' => $this->resolveConversionQty($item),
                    'is_active' => (int) Arr::get($item, 'is_active', 1),
                    'is_base' => 0,
                ];
            })
            ->filter(function (array $item) use ($baseUnitId) {
                return $item['unit_id'] > 0
                    && $item['unit_id'] !== $baseUnitId
                    && is_numeric($item['conversion_qty'])
                    && (float) $item['conversion_qty'] > 0;
            })
            ->unique('unit_id')
            ->values()
            ->prepend([
                'unit_id' => $baseUnitId,
                'conversion_qty' => 1,
                'is_active' => 1,
                'is_base' => 1,
            ])
            ->all();

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
