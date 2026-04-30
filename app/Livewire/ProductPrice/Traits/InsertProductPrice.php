<?php

namespace App\Livewire\ProductPrice\Traits;

trait InsertProductPrice
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

    public function insertProductPrice(): void
    {
        $this->prepareProductPriceFormForValidation('createForm');

        $this->validate(
            $this->createRules(),
            [],
            $this->formAttributes('createForm'),
        );

        $this->productPriceServices->createProductPrice(
            $this->normalizeProductPricePayload($this->createForm),
        );

        $this->createFeedback = __('Product price created successfully.');
        $this->createForm = $this->defaultForm();
        $this->resetErrorBag($this->formErrorKeys('createForm'));
        $this->resetPage();
    }
}
