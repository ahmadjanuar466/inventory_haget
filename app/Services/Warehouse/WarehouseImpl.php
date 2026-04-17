<?php

namespace App\Services\Warehouse;

use App\Models\Warehouse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

class WarehouseImpl implements WarehouseServices
{
    public function createWarehouse(array $attributes): Warehouse
    {
        return Warehouse::create($this->preparePayload($attributes));
    }

    public function updateWarehouse(Warehouse $warehouse, array $attributes): Warehouse
    {
        $warehouse->update($this->preparePayload($attributes));

        return $warehouse->refresh();
    }

    public function deleteWarehouse(Warehouse $warehouse): bool
    {
        if (
            $warehouse->stocks()->exists()
            || $warehouse->stockMovements()->exists()
            || $warehouse->purchaseReceipts()->exists()
        ) {
            return false;
        }

        return (bool) $warehouse->delete();
    }

    public function getWarehouseById(int $id): Warehouse
    {
        return Warehouse::query()->with('branch')->findOrFail($id);
    }

    public function paginateWarehouses(string $search = '', int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        $query = Warehouse::query()
            ->with('branch')
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($subQuery) use ($search) {
                    $subQuery->where('code', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                });
            })
            ->when(isset($filters['branch_id']) && $filters['branch_id'] !== '', function ($q) use ($filters) {
                $q->where('branch_id', $filters['branch_id']);
            })
            ->when(isset($filters['is_active']) && $filters['is_active'] !== '', function ($q) use ($filters) {
                $q->where('is_active', $filters['is_active']);
            });

        return $query->paginate($perPage);
    }

    protected function preparePayload(array $attributes): array
    {
        return Arr::only($attributes, [
            'branch_id',
            'code',
            'name',
            'is_active',
        ]);
    }
}
