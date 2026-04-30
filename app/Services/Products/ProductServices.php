<?php

namespace App\Services\Products;

use App\Models\Products;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProductServices
{
    //
    public function createProduct(array $attributes): Products;
    public function updateProduct(Products $product, array $attributes): Products;
    public function deleteProduct(Products $product): bool;
    public function getProductById(int $id): Products;
    public function paginateProducts(string $search = '', int $perPage = 10, array $filters = []): LengthAwarePaginator;
}
