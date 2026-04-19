<?php

namespace App\Livewire\Product\Traits;

trait ProductUpdate
{
    public function startEditing(int $productId): void
    {
        if ($this->showCreateModal) {
            $this->closeCreateModal();
        }

        if ($this->showDeleteModal) {
            $this->cancelDelete();
        }

        $product = $this->productServices->getProductsById($productId);

        $this->editingProductId = $productId;
        $this->editFeedback = '';
        $this->editForm = [
            'sku' => $product->sku,
            'name' => $product->name,
            'category_id' => $product->category_id,
            'units_id' => $product->units_id,
            'track_stock' => (int) $product->track_stock,
            'has_expiry' => (int) $product->has_expiry,
            'cost_price' => $product->cost_price,
            'sell_price' => $product->sell_price,
            'min_stock' => $product->min_stock,
            'is_active' => (int) $product->is_active,
        ];

        $this->resetErrorBag($this->formErrorKeys('editForm'));
        $this->showEditModal = true;
    }

    public function cancelEditing(): void
    {
        $this->showEditModal = false;
        $this->editingProductId = null;
        $this->editFeedback = '';
        $this->editForm = $this->defaultForm();
        $this->resetErrorBag($this->formErrorKeys('editForm'));
    }

    public function updateProduct(): void
    {
        if (! $this->editingProductId) {
            return;
        }

        $product = $this->productServices->getProductsById($this->editingProductId);

        $this->validate(
            $this->updateRules($product->id),
            [],
            $this->formAttributes('editForm'),
        );

        $this->productServices->updateProducts($product, $this->editForm);

        $this->editFeedback = __('Product updated successfully.');
        $this->resetErrorBag($this->formErrorKeys('editForm'));
        $this->resetPage();
    }
}
