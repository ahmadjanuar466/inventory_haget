<?php

namespace App\Services\Products;

use App\Models\Products;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProductServices
{
    //
    public function createProducts(array $attributes): Products;
    public function updateProducts(Products $product, array $attributes): Products;
    public function deleteProducts(Products $product): bool;
    public function getProductsById(int $id): Products;
    public function paginateProducts(string $search = '', int $perPage = 10, array $filters = []): LengthAwarePaginator;
}
