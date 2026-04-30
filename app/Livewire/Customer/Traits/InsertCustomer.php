<?php

namespace App\Livewire\Customer\Traits;

trait InsertCustomer
{


    public function openCreateModal(): void
    {
        if ($this->showEditModal) {
            $this->cancelEditing();
        }

        if ($this->showDeleteModal) {
            $this->cancelDelete();
        }

        $this->createForm = $this->defaultForm();
        $this->createFeedback = '';
        $this->resetErrorBag($this->formErrorKeys('createForm'));
        $this->showCreateModal = true;
    }
    public function closeCreateModal(): void
    {
        $this->showCreateModal = false;
        $this->createForm = $this->defaultForm();
        $this->createFeedback = '';
        $this->resetErrorBag($this->formErrorKeys('createForm'));
    }

    public function insertCustomer(): void
    {
        $this->prepareCustomerFormForValidation('createForm');

        $this->validate(
            $this->createRules(),
            [],
            $this->formAttributes('createForm'),
        );

        $this->customerServices->createCustomer(
            $this->normalizeCustomerPayload($this->createForm),
        );

        $this->createFeedback = __('Customer created successfully.');
        $this->createForm = $this->defaultForm();
        $this->resetErrorBag($this->formErrorKeys('createForm'));
        $this->resetPage();
    }
}
