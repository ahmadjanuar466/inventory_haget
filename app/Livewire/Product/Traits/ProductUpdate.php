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

        $product = $this->productServices->getProductById($productId);

        $this->editingProductId = $productId;
        $this->editFeedback = '';
        $productUnit = $product->productUnits->firstWhere('is_active', 1)
            ?? $product->productUnits->first();

        $productUnits = [[
            'unit_id' => $productUnit ? (string) $productUnit->unit_id : (string) $product->units_id,
            'conversion_qty' => $productUnit ? (string) $productUnit->conversion_qty : '',
            'is_active' => $productUnit ? (string) $productUnit->is_active : '1',
        ]];

        $this->editForm = [
            'sku' => $product->sku,
            'name' => $product->name,
            'category_id' => (string) $product->category_id,
            'units_id' => (string) $product->units_id,
            'product_units' => $productUnits,
            'track_stock' => (string) $product->track_stock,
            'has_expiry' => (string) $product->has_expiry,
            'min_stock' => $product->min_stock !== null ? (string) $product->min_stock : '',
            'is_active' => (string) $product->is_active,
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

        $product = $this->productServices->getProductById($this->editingProductId);

        $this->prepareProductFormForValidation('editForm');

        $this->validate(
            $this->updateRules($product->id),
            [],
            $this->formAttributes('editForm'),
        );

        $this->productServices->updateProduct(
            $product,
            $this->normalizeProductPayload($this->editForm),
        );

        $this->editFeedback = __('Product updated successfully.');
        $this->resetErrorBag($this->formErrorKeys('editForm'));
        $this->resetPage();
    }
}
