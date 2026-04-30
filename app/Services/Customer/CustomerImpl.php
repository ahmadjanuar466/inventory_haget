<?php

namespace App\Services\Customer;

use App\Models\Customers;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class CustomerImpl implements CustomerServices
{
    private const CUSTOMER_CODE_PREFIX = 'CUST-';

    public function createCustomer(array $attributes): Customers
    {
        return DB::transaction(function () use ($attributes) {
            $payload = $this->preparePayload($attributes);

            if (empty(trim((string) ($payload['code'] ?? '')))) {
                $payload['code'] = $this->generateCustomerCode();
            }

            return Customers::create($payload);
        });
    }
    public function updateCustomer(Customers $customer, array $attributes): Customers
    {
        $customer->update($this->preparePayload($attributes));
        return $customer->refresh();
    }
    public function deleteCustomer(Customers $customer): bool
    {
        return (bool) $customer->delete();
    }
    public function getCustomerById(int $id): Customers
    {
        return Customers::with('customerType')->findOrFail($id);
    }
    public function paginateCustomers(string $search, int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Customers::query()->with('customerType')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('code', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%");
                });
            })
            ->when(isset($filters['customer_type_id']) && $filters['customer_type_id'] !== '', function ($query) use ($filters) {
                $query->where('customer_type_id', $filters['customer_type_id']);
            })
            ->when(isset($filters['is_active']) && $filters['is_active'] !== '', function ($query) use ($filters) {
                $query->where('is_active', $filters['is_active']);
            })
            ->orderBy('name');
        return $query->paginate($perPage);
    }
    protected function preparePayload(array $attributes): array
    {
        return Arr::only($attributes, [
            'code',
            'name',
            'phone',
            'email',
            'address',
            'customer_type_id',
            'is_active'
        ]);
    }

    protected function generateCustomerCode(): string
    {
        $lastNumber = Customers::query()
            ->where('code', 'like', self::CUSTOMER_CODE_PREFIX . '%')
            ->lockForUpdate()
            ->pluck('code')
            ->map(function (string $code): int {
                $number = preg_replace('/\D/', '', substr($code, strlen(self::CUSTOMER_CODE_PREFIX)));

                return $number === '' ? 0 : (int) $number;
            })
            ->max() ?? 0;

        return self::CUSTOMER_CODE_PREFIX . str_pad((string) ($lastNumber + 1), 6, '0', STR_PAD_LEFT);
    }
}
