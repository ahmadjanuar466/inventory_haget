<?php

namespace App\Livewire\CustomerType\Traits;

trait DeleteCustomerType
{
    public function confirmDelete(int $customerTypeId): void
    {
        if ($this->showCreateModal) {
            $this->closeCreateModal();
        }

        if ($this->showEditModal) {
            $this->cancelEditing();
        }

        $customerType = $this->customerTypeServices->getCustomerTypeById($customerTypeId);

        $this->deletingCustomerTypeId = $customerTypeId;
        $this->deleteContextName = $customerType->name;
        $this->deleteFeedback = '';
        $this->resetErrorBag(['delete']);
        $this->showDeleteModal = true;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deletingCustomerTypeId = null;
        $this->deleteContextName = null;
        $this->deleteFeedback = '';
        $this->resetErrorBag(['delete']);
    }

    public function deleteCustomerType(): void
    {
        if (! $this->deletingCustomerTypeId) {
            return;
        }

        $customerType = $this->customerTypeServices->getCustomerTypeById($this->deletingCustomerTypeId);
        $name = $this->deleteContextName ?? $customerType->name;

        if (! $this->customerTypeServices->deleteCustomerType($customerType)) {
            $this->addError('delete', __('Customer type cannot be deleted because it is already used by customers.'));

            return;
        }

        $this->deleteFeedback = __('Customer type ":name" deleted successfully.', ['name' => $name]);
        $this->deletingCustomerTypeId = null;
        $this->deleteContextName = null;
        $this->showDeleteModal = false;
        $this->resetPage();
    }
}
