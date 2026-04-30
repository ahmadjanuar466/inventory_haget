<?php

namespace App\Livewire\ProductPrice\Traits;

trait UpdateProductPrice
{
    public function startEditing(int $productPriceId): void
    {
        if ($this->showCreateModal) {
            $this->closeCreateModal();
        }

        if ($this->showDeleteModal) {
            $this->cancelDelete();
        }

        $productPrice = $this->productPriceServices->getProductPriceById($productPriceId);

        $this->editingProductPriceId = $productPriceId;
        $this->editFeedback = '';
        $this->editForm = [
            'product_id' => (string) $productPrice->product_id,
            'price' => (string) $productPrice->price,
            'is_active' => (string) (int) $productPrice->is_active,
        ];

        $this->resetErrorBag($this->formErrorKeys('editForm'));
        $this->showEditModal = true;
    }

    public function cancelEditing(): void
    {
        $this->showEditModal = false;
        $this->editingProductPriceId = null;
        $this->editFeedback = '';
        $this->editForm = $this->defaultForm();
        $this->resetErrorBag($this->formErrorKeys('editForm'));
    }

    public function updateProductPrice(): void
    {
        if (! $this->editingProductPriceId) {
            return;
        }

        $productPrice = $this->productPriceServices->getProductPriceById($this->editingProductPriceId);

        $this->prepareProductPriceFormForValidation('editForm');

        $this->validate(
            $this->updateRules(),
            [],
            $this->formAttributes('editForm'),
        );

        $this->productPriceServices->updateProductPrice(
            $productPrice,
            $this->normalizeProductPricePayload($this->editForm),
        );

        $this->editFeedback = __('Product price updated successfully.');
        $this->resetErrorBag($this->formErrorKeys('editForm'));
        $this->resetPage();
    }
}
