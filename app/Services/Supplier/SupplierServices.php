<?php

namespace App\Services\Supplier;

use App\Models\Suppliers;
use Illuminate\Pagination\LengthAwarePaginator;

interface SupplierServices
{
    //
    public function createSupplier(array $attributes): Suppliers;
    public function updateSupplier(Suppliers $supplier, array $attributes): Suppliers;
    public function deleteSupplier(Suppliers $supplier): bool;
    public function getSupplierById(int $id): Suppliers;
    public function paginateSuppliers(string $search = '', int $perPage = 10, array $filters = []): LengthAwarePaginator;
}
