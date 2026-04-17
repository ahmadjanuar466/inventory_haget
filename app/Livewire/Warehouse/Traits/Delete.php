<?php

namespace App\Livewire\Warehouse\Traits;

use Illuminate\Database\QueryException;

trait Delete
{
    public function confirmDelete(int $warehouseId): void
    {
        if ($this->showCreateModal) {
            $this->closeCreateModal();
        }

        if ($this->showEditModal) {
            $this->cancelEditing();
        }

        $warehouse = $this->warehouseServices->getWarehouseById($warehouseId);

        $this->deletingWarehouseId = $warehouseId;
        $this->deleteContextName = $warehouse->name;
        $this->deleteFeedback = '';
        $this->resetErrorBag(['delete']);
        $this->showDeleteModal = true;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deletingWarehouseId = null;
        $this->deleteContextName = null;
        $this->deleteFeedback = '';
        $this->resetErrorBag(['delete']);
    }

    public function deleteWarehouse(): void
    {
        if (! $this->deletingWarehouseId) {
            return;
        }

        $warehouse = $this->warehouseServices->getWarehouseById($this->deletingWarehouseId);
        $name = $this->deleteContextName ?? $warehouse->name;

        try {
            $deleted = $this->warehouseServices->deleteWarehouse($warehouse);
        } catch (QueryException) {
            $this->addError('delete', __('Warehouse cannot be deleted because it is already used by transactions. Deactivate it instead.'));

            return;
        }

        if (! $deleted) {
            $this->addError('delete', __('Warehouse cannot be deleted because it is already used by stock or purchase transactions. Deactivate it instead.'));

            return;
        }

        $this->deleteFeedback = __('Warehouse ":name" deleted successfully.', ['name' => $name]);
        $this->deletingWarehouseId = null;
        $this->deleteContextName = null;
        $this->showDeleteModal = false;
        $this->resetPage();
    }
}
