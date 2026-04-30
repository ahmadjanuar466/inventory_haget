<?php

namespace App\Livewire\Supplier\Traits;

trait UpdateSupplier
{
    public function startEditing(int $supplierId): void
    {
        if ($this->showCreateModal) {
            $this->closeCreateModal();
        }

        if ($this->showDeleteModal) {
            $this->cancelDelete();
        }

        $supplier = $this->supplierServices->getSupplierById($supplierId);

        $this->editingSupplierId = $supplierId;
        $this->editFeedback = '';
        $this->editForm = [
            'code' => $supplier->code,
            'name' => $supplier->name,
            'contact_person' => $supplier->contact_person ?? '',
            'phone' => $supplier->phone ?? '',
            'email' => $supplier->email ?? '',
            'address' => $supplier->address ?? '',
            'is_active' => (string) (int) $supplier->is_active,
        ];

        $this->resetErrorBag($this->formErrorKeys('editForm'));
        $this->showEditModal = true;
    }

    public function cancelEditing(): void
    {
        $this->showEditModal = false;
        $this->editingSupplierId = null;
        $this->editFeedback = '';
        $this->editForm = $this->defaultForm();
        $this->resetErrorBag($this->formErrorKeys('editForm'));
    }

    public function updateSupplier(): void
    {
        if (! $this->editingSupplierId) {
            return;
        }

        $supplier = $this->supplierServices->getSupplierById($this->editingSupplierId);

        $this->validate(
            $this->updateRules($supplier->id),
            [],
            $this->formAttributes('editForm'),
        );

        $this->supplierServices->updateSupplier(
            $supplier,
            $this->normalizeSupplierPayload($this->editForm),
        );

        $this->editFeedback = __('Supplier updated successfully.');
        $this->resetErrorBag($this->formErrorKeys('editForm'));
        $this->resetPage();
    }
}
