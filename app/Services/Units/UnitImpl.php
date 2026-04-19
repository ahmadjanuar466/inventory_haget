<?php

namespace App\Services\Units;

use App\Models\Units;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

class UnitImpl implements UnitServices
{
    public function createUnits(array $attributes): Units
    {
        return Units::create($this->preparePayload($attributes));
    }

    public function updateUnits(Units $units, array $attributes): Units
    {
        $units->update($this->preparePayload($attributes));

        return $units->refresh();
    }

    public function deleteUnits(Units $units): bool
    {
        if ($units->products()->exists()) {
            return false;
        }

        return (bool) $units->delete();
    }

    public function getUnitsById(int $id): Units
    {
        return Units::query()->with('products')->findOrFail($id);
    }

    public function paginateUnits(string $search = '', int $perPage = 10): LengthAwarePaginator
    {
        return Units::query()
            ->with('products')
            ->withCount('products')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate($perPage);
    }

    protected function preparePayload(array $attributes): array
    {
        return Arr::only($attributes, ['code', 'name']);
    }
}
