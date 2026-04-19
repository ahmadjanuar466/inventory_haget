<?php

namespace App\Services\Categories;

use App\Models\Categories;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

class CategoryImpl implements CategoryServices
{
    public function createCategory(array $attributes): Categories
    {
        return Categories::create($this->preparePayload($attributes));
    }

    public function updateCategory(Categories $category, array $attributes): Categories
    {
        $category->update($this->preparePayload($attributes));

        return $category->refresh();
    }

    public function deleteCategory(Categories $category): bool
    {
        if ($category->products()->exists() || $category->children()->exists()) {
            return false;
        }

        return (bool) $category->delete();
    }

    public function getCategoryById(int $id): Categories
    {
        return Categories::query()->with('parent')->findOrFail($id);
    }

    public function paginateCategories(string $search = '', int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        $query = Categories::query()
            ->with('parent')
            ->when(
                $search !== '',
                function ($query) use ($search) {
                    $query->where(function ($subQuery) use ($search) {
                        $subQuery->where('code', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%");
                    });
                }
            )->when(
                isset($filters['parent_id']) && $filters['parent_id'] !== '',
                function ($query) use ($filters) {
                    $query->where('parent_id', $filters['parent_id']);
                }
            )
            ->orderBy('name');

        return $query->paginate($perPage);
    }

    protected function preparePayload(array $attributes): array
    {
        $payload = Arr::only(
            $attributes,
            [
                'parent_id',
                'code',
                'name',
            ]
        );

        if (($payload['parent_id'] ?? '') === '') {
            $payload['parent_id'] = null;
        }

        return $payload;
    }
}
