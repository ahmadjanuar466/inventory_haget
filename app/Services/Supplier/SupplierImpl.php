<?php

namespace App\Services\Supplier;

use App\Models\Suppliers;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

class SupplierImpl implements SupplierServices
{
    public function createSupplier(array $attributes): Suppliers
    {
        return Suppliers::create($this->preparePayload($attributes));
    }
    public function updateSupplier(Suppliers $supplier, array $attributes): Suppliers
    {
        $supplier->update($this->preparePayload($attributes));
        return $supplier->refresh();
    }
    public function deleteSupplier(Suppliers $supplier): bool
    {
        if ($supplier->purchaseReceipts()->exists()) {
            return false;
        }

        return (bool) $supplier->delete();
    }
    public function getSupplierById(int $id): Suppliers
    {
        return Suppliers::with('purchaseReceipts')->findOrFail($id);
    }
    public function paginateSuppliers(string $search = '', int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        $query = Suppliers::query()
            ->with('purchaseReceipts')
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('contact_person', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%");
                });
            })
            ->when(isset($filters['is_active']) && $filters['is_active'] !== '', function ($q) use ($filters) {
                $q->where('is_active', $filters['is_active']);
            });

        return $query->paginate($perPage);
    }
    protected function preparePayload(array $attributes): array
    {
        return Arr::only($attributes, [
            'code',
            'name',
            'contact_person',
            'phone',
            'address',
            'email',
            'is_active'
        ]);
    }
}
