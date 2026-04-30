<?php

namespace App\Services\Customer;

use App\Models\CustomerType;
use Illuminate\Pagination\LengthAwarePaginator;

interface CustomerTypeServices
{
    //
    public function createCustomerType(array $attributes): CustomerType;
    public function updateCustomerType(CustomerType $customerType, array $attributes): CustomerType;
    public function deleteCustomerType(CustomerType $customerType): bool;
    public function getCustomerTypeById(int $id): CustomerType;
    public function paginateCustomerTypes(string $search = '', int $perPage = 10): LengthAwarePaginator;
}
