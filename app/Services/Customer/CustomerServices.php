<?php

namespace App\Services\Customer;

use App\Models\Customers;
use Illuminate\Pagination\LengthAwarePaginator;

interface CustomerServices
{
    //
    public function createCustomer(array $attributes): Customers;
    public function updateCustomer(Customers $customer, array $attributes): Customers;
    public function deleteCustomer(Customers $customer): bool;
    public function getCustomerById(int $id): Customers;
    public function paginateCustomers(string $search, int $perPage = 15, array $filters = []): LengthAwarePaginator;
}
