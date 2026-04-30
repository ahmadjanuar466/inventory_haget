<?php

namespace App\Livewire\Customer\Traits;

trait DeleteCustomer
{
    public function confirmDelete(int $customerId): void
    {
        if ($this->showCreateModal) {
            $this->closeCreateModal();
        }

        if ($this->showEditModal) {
            $this->cancelEditing();
        }

        $customer = $this->customerServices->getCustomerById($customerId);

        $this->deletingCustomerId = $customerId;
        $this->deleteContextName = $customer->name;
        $this->deleteFeedback = '';
        $this->resetErrorBag(['delete']);
        $this->showDeleteModal = true;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deletingCustomerId = null;
        $this->deleteContextName = null;
        $this->deleteFeedback = '';
        $this->resetErrorBag(['delete']);
    }

    public function deleteCustomer(): void
    {
        if (! $this->deletingCustomerId) {
            return;
        }

        $customer = $this->customerServices->getCustomerById($this->deletingCustomerId);
        $name = $this->deleteContextName ?? $customer->name;

        if (! $this->customerServices->deleteCustomer($customer)) {
            $this->addError('delete', __('Customer cannot be deleted.'));

            return;
        }

        $this->deleteFeedback = __('Customer ":name" deleted successfully.', ['name' => $name]);
        $this->deletingCustomerId = null;
        $this->deleteContextName = null;
        $this->showDeleteModal = false;
        $this->resetPage();
    }
}
