<?php

namespace App\Services\ProductPrices;

use App\Models\ProductPrice;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProductPriceServices
{
    //
    public function createProductPrice(array $attributes): ProductPrice;
    public function updateProductPrice(ProductPrice $productPrice, array $attributes): ProductPrice;
    public function deleteProductPrice(ProductPrice $productPrice): bool;
    public function getProductPriceById(int $id): ProductPrice;
    public function paginateProductPrices(string $search = '', int $perPage = 10, array $filters = []): LengthAwarePaginator;
}
