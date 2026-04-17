<?php

namespace App\Livewire\Warehouse\Traits;

trait Insert
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

    public function createWarehouse(): void
    {
        $this->validate(
            $this->createRules(),
            [],
            $this->formAttributes('createForm'),
        );

        $this->warehouseServices->createWarehouse($this->createForm);

        $this->createFeedback = __('Warehouse created successfully.');
        $this->createForm = $this->defaultForm();
        $this->resetErrorBag($this->formErrorKeys('createForm'));
        $this->resetPage();
    }
}
