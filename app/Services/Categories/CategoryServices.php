<?php

namespace App\Services\Categories;

use App\Models\Categories;
use Illuminate\Pagination\LengthAwarePaginator;

interface CategoryServices
{
    public function createCategory(array $attributes): Categories;

    public function updateCategory(Categories $category, array $attributes): Categories;

    public function deleteCategory(Categories $category): bool;

    public function getCategoryById(int $id): Categories;

    public function paginateCategories(string $search = '', int $perPage = 10, array $filters = []): LengthAwarePaginator;
}
