<?php

namespace App\Services\Branch;

use App\Models\Branches;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

class BranchImpl implements BranchServices
{

    /**
     * Persist a branch with the given attributes.
     */
    public function createBranch(array $attributes): Branches
    {
        return Branches::create($this->preparePayload($attributes));
    }
    /**
     * Update the provided branch.
     */
    public function updateBranch(Branches $branch, array $attributes): Branches
    {
        $branch->update($this->preparePayload($attributes));
        return $branch->refresh();
    }
    /**
     * Delete the provided branch.
     */
    public function deleteBranch(Branches $branch): bool
    {
        return (bool) $branch->delete();
    }
    /**
     * Get a branch by its ID.
     */
    public function getBranchesById(int $id): Branches
    {
        return Branches::query()->with('branchtype')->findOrFail($id);
    }
    /**
     * Paginate the branches based on the provided parameters.
     */
    public function paginateBranches(string $search = '', int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        $query = Branches::query()
            ->with('branchtype')
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($subQuery) use ($search) {
                    $subQuery->where('code', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when(isset($filters['branch_type_id']) && $filters['branch_type_id'] !== '', function ($q) use ($filters) {
                $q->where('branch_type_id', $filters['branch_type_id']);
            })
            ->when(isset($filters['status']) && $filters['status'] !== '', function ($q) use ($filters) {
                $q->where('status', $filters['status']);
            })
            ->orderBy('name');


        return $query->paginate($perPage);
    }
    /**
     * Prepare the payload for creating or updating a branch.
     */
    protected function preparePayload(array $attributes): array
    {
        return Arr::only($attributes, [
            'code',
            'name',
            'address',
            'phone',
            'branch_type_id',
            'status',
        ]);
    }
}
