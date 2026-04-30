<?php

namespace App\Livewire\ProductPrice\Traits;

trait DeleteProductPrice
{
    public function confirmDelete(int $productPriceId): void
    {
        if ($this->showCreateModal) {
            $this->closeCreateModal();
        }

        if ($this->showEditModal) {
            $this->cancelEditing();
        }

        $productPrice = $this->productPriceServices->getProductPriceById($productPriceId);

        $this->deletingProductPriceId = $productPriceId;
        $this->deleteContextName = $productPrice->product?->name ?? __('Product Price');
        $this->deleteFeedback = '';
        $this->resetErrorBag(['delete']);
        $this->showDeleteModal = true;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deletingProductPriceId = null;
        $this->deleteContextName = null;
        $this->deleteFeedback = '';
        $this->resetErrorBag(['delete']);
    }

    public function deleteProductPrice(): void
    {
        if (! $this->deletingProductPriceId) {
            return;
        }

        $productPrice = $this->productPriceServices->getProductPriceById($this->deletingProductPriceId);
        $name = $this->deleteContextName ?? $productPrice->product?->name ?? __('Product Price');

        $this->productPriceServices->deleteProductPrice($productPrice);

        $this->deleteFeedback = __('Product price for ":name" deleted successfully.', ['name' => $name]);
        $this->deletingProductPriceId = null;
        $this->deleteContextName = null;
        $this->showDeleteModal = false;
        $this->resetPage();
    }
}
