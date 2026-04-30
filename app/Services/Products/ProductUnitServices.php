<?php

namespace App\Services\Products;

use App\Models\Products;

interface ProductUnitServices
{
    public function syncProductUnits(Products $product, int $baseUnitId, array $attributes = []): void;
}
