<?php

namespace App\Livewire\Branch\Traits;

trait Update
{
    public function startEditing(int $branchId): void
    {
        if ($this->showCreateModal) {
            $this->closeCreateModal();
        }

        if ($this->showDeleteModal) {
            $this->cancelDelete();
        }

        $branch = $this->branchServices->getBranchesById($branchId);

        $this->editingBranchId = $branchId;
        $this->editFeedback = '';
        $this->editForm = [
            'code' => $branch->code,
            'name' => $branch->name,
            'branch_type_id' => (string) $branch->branch_type_id,
            'address' => $branch->address ?? '',
            'phone' => $branch->phone ?? '',
            'status' => (string) $branch->status,
        ];

        $this->resetErrorBag($this->formErrorKeys('editForm'));
        $this->showEditModal = true;
    }

    public function cancelEditing(): void
    {
        $this->showEditModal = false;
        $this->editingBranchId = null;
        $this->editFeedback = '';
        $this->editForm = $this->defaultForm();
        $this->resetErrorBag($this->formErrorKeys('editForm'));
    }

    public function updateBranch(): void
    {
        if (! $this->editingBranchId) {
            return;
        }

        $branch = $this->branchServices->getBranchesById($this->editingBranchId);

        $this->validate(
            $this->updateRules($branch->id),
            [],
            $this->formAttributes('editForm'),
        );

        $this->branchServices->updateBranch($branch, $this->editForm);

        $this->editFeedback = __('Branch updated successfully.');
        $this->resetErrorBag($this->formErrorKeys('editForm'));
        $this->resetPage();
    }
}
