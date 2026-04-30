<?php

namespace App\Services\Customer;

use App\Models\CustomerType;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

class CustomerTypeImpl implements CustomerTypeServices
{
    public function createCustomerType(array $attributes): CustomerType
    {
        return CustomerType::create($this->preparePayload($attributes));
    }
    public function updateCustomerType(CustomerType $customerType, array $attributes): CustomerType
    {
        $customerType->update($this->preparePayload($attributes));
        return $customerType->refresh();
    }
    public function deleteCustomerType(CustomerType $customerType): bool
    {
        if ($customerType->customers()->exists()) {
            return false;
        }

        return (bool) $customerType->delete();
    }
    public function getCustomerTypeById(int $id): CustomerType
    {
        return CustomerType::with('customers')->findOrFail($id);
    }
    public function paginateCustomerTypes(string $search = '', int $perPage = 10): LengthAwarePaginator
    {
        $query = CustomerType::query()
            ->with('customers')
            ->withCount('customers')
            ->when($search !== '', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        return $query->paginate($perPage);
    }
    protected function preparePayload(array $attributes): array
    {
        return Arr::only($attributes, ['name']);
    }
}
