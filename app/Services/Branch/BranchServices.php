<?php

namespace App\Services\Branch;

use App\Models\Branches;
use Illuminate\Pagination\LengthAwarePaginator;

interface BranchServices
{
    //
    public function createBranch(array $attributes): Branches;
    public function updateBranch(Branches $branch, array $attributes): Branches;
    public function deleteBranch(Branches $branch): bool;
    public function getBranchesById(int $id): Branches;
    public function paginateBranches(string $search = '', int $perPage = 10, array $filters = []): LengthAwarePaginator;
}
