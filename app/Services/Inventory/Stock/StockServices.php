<?php

namespace App\Services\Inventory\Stock;

use App\Models\Stocks;
use Illuminate\Pagination\LengthAwarePaginator;

interface StockServices
{
    public function addStock(array $data): Stocks;

    public function increaseStock(Stocks $stock, array $data): Stocks;

    public function decreaseStock(Stocks $stock, array $data): Stocks;

    public function getCurrentStock(int $warehouseId, int $productId): Stocks;

    public function paginateStock(string $search = '', int $perPage = 10, array $filters = []): LengthAwarePaginator;
}
