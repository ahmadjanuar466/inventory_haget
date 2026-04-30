<?php

namespace App\Services\Inventory\Stock;

use App\Models\StockMovements;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

class StockMovementImpl implements StockMovementServices
{
    public function recordStockMovement(array $data): StockMovements
    {
        return StockMovements::create($this->preparePayload($data));
    }
    public function updateStockMovement(StockMovements $stockMovements, array $data): StockMovements
    {
        $stockMovements->update($this->preparePayload($data));
        return $stockMovements->refresh();
    }
    public function deleteStockMovement(StockMovements $stockMovements): bool
    {
        return (bool) $stockMovements->delete();
    }
    public function getStockMovementById(int $id): StockMovements
    {
        return StockMovements::with(['product', 'warehouse', 'creator'])->findOrFail($id);
    }
    public function getStockMovementsByProductId(int $productId): array
    {
        return StockMovements::with(['product', 'warehouse', 'creator'])
            ->where('product_id', $productId)
            ->orderByDesc('movement_date')
            ->get()
            ->toArray();
    }
    public function paginateStockMovements(string $search = '', int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        $query = StockMovements::query()->with(['product', 'warehouse', 'creator'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('product', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                        ->orWhereHas('warehouse', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('creator', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        })
                        ->orWhere('notes', 'like', "%{$search}%");
                });
            })->when(! empty($filters['product_id']), function ($query) use ($filters) {
                $query->where('product_id', $filters['product_id']);
            })->when(! empty($filters['warehouse_id']), function ($query) use ($filters) {
                $query->where('warehouse_id', $filters['warehouse_id']);
            })->when(! empty($filters['movement_type']), function ($query) use ($filters) {
                $query->where('movement_type', $filters['movement_type']);
            });
        return $query->orderByDesc('movement_date')->paginate($perPage);
    }
    protected function preparePayload(array $data): array
    {
        return Arr::only($data, [
            'movement_date',
            'warehouse_id',
            'product_id',
            'reference_type',
            'reference_id',
            'movement_type',
            'qty_in',
            'qty_out',
            'qty_balance_after',
            'unit_cost',
            'notes',
            'created_by',
        ]);
    }
}
