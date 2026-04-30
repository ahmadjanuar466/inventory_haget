<?php

namespace App\Livewire\CustomerType\Traits;

trait UpdateCustomerType
{
    public function startEditing(int $customerTypeId): void
    {
        if ($this->showCreateModal) {
            $this->closeCreateModal();
        }

        if ($this->showDeleteModal) {
            $this->cancelDelete();
        }

        $customerType = $this->customerTypeServices->getCustomerTypeById($customerTypeId);

        $this->editingCustomerTypeId = $customerTypeId;
        $this->editFeedback = '';
        $this->editForm = [
            'name' => $customerType->name,
        ];

        $this->resetErrorBag($this->formErrorKeys('editForm'));
        $this->showEditModal = true;
    }

    public function cancelEditing(): void
    {
        $this->showEditModal = false;
        $this->editingCustomerTypeId = null;
        $this->editFeedback = '';
        $this->editForm = $this->defaultForm();
        $this->resetErrorBag($this->formErrorKeys('editForm'));
    }

    public function updateCustomerType(): void
    {
        if (! $this->editingCustomerTypeId) {
            return;
        }

        $customerType = $this->customerTypeServices->getCustomerTypeById($this->editingCustomerTypeId);

        $this->validate(
            $this->updateRules($customerType->id),
            [],
            $this->formAttributes('editForm'),
        );

        $this->customerTypeServices->updateCustomerType(
            $customerType,
            $this->normalizeCustomerTypePayload($this->editForm),
        );

        $this->editFeedback = __('Customer type updated successfully.');
        $this->resetErrorBag($this->formErrorKeys('editForm'));
        $this->resetPage();
    }
}
