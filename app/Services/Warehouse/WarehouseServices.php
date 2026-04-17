<?php

namespace App\Services\Warehouse;

use App\Models\Warehouse;
use Illuminate\Pagination\LengthAwarePaginator;

interface WarehouseServices
{
    //
    public function createWarehouse(array $attributes): Warehouse;

    public function updateWarehouse(Warehouse $warehouse, array $attributes): Warehouse;

    public function deleteWarehouse(Warehouse $warehouse): bool;

    public function getWarehouseById(int $id): Warehouse;

    public function paginateWarehouses(string $search = '', int $perPage = 10, array $filters = []): LengthAwarePaginator;
}
