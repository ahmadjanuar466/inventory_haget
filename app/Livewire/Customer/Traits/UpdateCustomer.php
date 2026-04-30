<?php

namespace App\Livewire\Customer\Traits;

trait UpdateCustomer
{
    public function startEditing(int $customerId): void
    {
        if ($this->showCreateModal) {
            $this->closeCreateModal();
        }

        if ($this->showDeleteModal) {
            $this->cancelDelete();
        }

        $customer = $this->customerServices->getCustomerById($customerId);

        $this->editingCustomerId = $customerId;
        $this->editFeedback = '';
        $this->editForm = [
            'code' => $customer->code,
            'name' => $customer->name,
            'phone' => $customer->phone ?? '',
            'email' => $customer->email ?? '',
            'address' => $customer->address ?? '',
            'customer_type_id' => (string) $customer->customer_type_id,
            'is_active' => (string) (int) $customer->is_active,
        ];

        $this->resetErrorBag($this->formErrorKeys('editForm'));
        $this->showEditModal = true;
    }

    public function cancelEditing(): void
    {
        $this->showEditModal = false;
        $this->editingCustomerId = null;
        $this->editFeedback = '';
        $this->editForm = $this->defaultForm();
        $this->resetErrorBag($this->formErrorKeys('editForm'));
    }

    public function updateCustomer(): void
    {
        if (! $this->editingCustomerId) {
            return;
        }

        $customer = $this->customerServices->getCustomerById($this->editingCustomerId);

        $this->prepareCustomerFormForValidation('editForm');

        $this->validate(
            $this->updateRules($customer->id),
            [],
            $this->formAttributes('editForm'),
        );

        $this->customerServices->updateCustomer(
            $customer,
            $this->normalizeCustomerPayload($this->editForm),
        );

        $this->editFeedback = __('Customer updated successfully.');
        $this->resetErrorBag($this->formErrorKeys('editForm'));
        $this->resetPage();
    }
}
