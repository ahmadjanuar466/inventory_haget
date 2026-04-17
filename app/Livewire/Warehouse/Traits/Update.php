<?php

namespace App\Livewire\Warehouse\Traits;

trait Update
{
    public function startEditing(int $warehouseId): void
    {
        if ($this->showCreateModal) {
            $this->closeCreateModal();
        }

        if ($this->showDeleteModal) {
            $this->cancelDelete();
        }

        $warehouse = $this->warehouseServices->getWarehouseById($warehouseId);

        $this->editingWarehouseId = $warehouseId;
        $this->editFeedback = '';
        $this->editForm = [
            'branch_id' => (string) $warehouse->branch_id,
            'code' => $warehouse->code,
            'name' => $warehouse->name,
            'is_active' => (string) $warehouse->is_active,
        ];

        $this->resetErrorBag($this->formErrorKeys('editForm'));
        $this->showEditModal = true;
    }

    public function cancelEditing(): void
    {
        $this->showEditModal = false;
        $this->editingWarehouseId = null;
        $this->editFeedback = '';
        $this->editForm = $this->defaultForm();
        $this->resetErrorBag($this->formErrorKeys('editForm'));
    }

    public function updateWarehouse(): void
    {
        if (! $this->editingWarehouseId) {
            return;
        }

        $warehouse = $this->warehouseServices->getWarehouseById($this->editingWarehouseId);

        $this->validate(
            $this->updateRules($warehouse->id),
            [],
            $this->formAttributes('editForm'),
        );

        $this->warehouseServices->updateWarehouse($warehouse, $this->editForm);

        $this->editFeedback = __('Warehouse updated successfully.');
        $this->resetErrorBag($this->formErrorKeys('editForm'));
        $this->resetPage();
    }
}
