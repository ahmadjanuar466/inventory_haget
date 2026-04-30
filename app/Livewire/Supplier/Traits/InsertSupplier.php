<?php

namespace App\Livewire\Supplier\Traits;

trait InsertSupplier
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

    public function insertSupplier(): void
    {
        $this->validate(
            $this->createRules(),
            [],
            $this->formAttributes('createForm'),
        );

        $this->supplierServices->createSupplier(
            $this->normalizeSupplierPayload($this->createForm),
        );

        $this->createFeedback = __('Supplier created successfully.');
        $this->createForm = $this->defaultForm();
        $this->resetErrorBag($this->formErrorKeys('createForm'));
        $this->resetPage();
    }
}
