<?php

namespace App\Livewire\Supplier\Traits;

trait DeleteSupplier
{
    public function confirmDelete(int $supplierId): void
    {
        if ($this->showCreateModal) {
            $this->closeCreateModal();
        }

        if ($this->showEditModal) {
            $this->cancelEditing();
        }

        $supplier = $this->supplierServices->getSupplierById($supplierId);

        $this->deletingSupplierId = $supplierId;
        $this->deleteContextName = $supplier->name;
        $this->deleteFeedback = '';
        $this->resetErrorBag(['delete']);
        $this->showDeleteModal = true;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deletingSupplierId = null;
        $this->deleteContextName = null;
        $this->deleteFeedback = '';
        $this->resetErrorBag(['delete']);
    }

    public function deleteSupplier(): void
    {
        if (! $this->deletingSupplierId) {
            return;
        }

        $supplier = $this->supplierServices->getSupplierById($this->deletingSupplierId);
        $name = $this->deleteContextName ?? $supplier->name;

        if (! $this->supplierServices->deleteSupplier($supplier)) {
            $this->addError('delete', __('Supplier cannot be deleted because it is already used by purchase receipts.'));

            return;
        }

        $this->deleteFeedback = __('Supplier ":name" deleted successfully.', ['name' => $name]);
        $this->deletingSupplierId = null;
        $this->deleteContextName = null;
        $this->showDeleteModal = false;
        $this->resetPage();
    }
}
