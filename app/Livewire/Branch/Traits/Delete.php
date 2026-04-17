<?php

namespace App\Livewire\Branch\Traits;

trait Delete
{
    public function confirmDelete(int $branchId): void
    {
        if ($this->showCreateModal) {
            $this->closeCreateModal();
        }

        if ($this->showEditModal) {
            $this->cancelEditing();
        }

        $branch = $this->branchServices->getBranchesById($branchId);

        $this->deletingBranchId = $branchId;
        $this->deleteContextName = $branch->name;
        $this->deleteFeedback = '';
        $this->resetErrorBag(['delete']);
        $this->showDeleteModal = true;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deletingBranchId = null;
        $this->deleteContextName = null;
        $this->deleteFeedback = '';
        $this->resetErrorBag(['delete']);
    }

    public function deleteBranch(): void
    {
        if (! $this->deletingBranchId) {
            return;
        }

        $branch = $this->branchServices->getBranchesById($this->deletingBranchId);
        $name = $this->deleteContextName ?? $branch->name;

        $this->branchServices->deleteBranch($branch);

        $this->deleteFeedback = __('Branch ":name" deleted successfully.', ['name' => $name]);
        $this->deletingBranchId = null;
        $this->deleteContextName = null;
        $this->showDeleteModal = false;
        $this->resetPage();
    }
}
