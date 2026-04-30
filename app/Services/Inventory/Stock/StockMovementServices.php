<?php

namespace App\Services\Inventory\Stock;

use App\Models\StockMovements;
use Illuminate\Pagination\LengthAwarePaginator;

interface StockMovementServices
{
    //
    public function recordStockMovement(array $data): StockMovements;
    public function updateStockMovement(StockMovements $stockMovements, array $data): StockMovements;
    public function deleteStockMovement(StockMovements $stockMovements): bool;
    public function getStockMovementById(int $id): StockMovements;
    public function getStockMovementsByProductId(int $productId): array;
    public function paginateStockMovements(string $search = '', int $perPage = 10,  array $filters = []): LengthAwarePaginator;
}
