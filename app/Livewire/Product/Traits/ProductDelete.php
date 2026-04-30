<?php

namespace App\Livewire\Product\Traits;

trait ProductDelete
{
    public function confirmDelete(int $productId): void
    {
        if ($this->showCreateModal) {
            $this->closeCreateModal();
        }

        if ($this->showEditModal) {
            $this->cancelEditing();
        }

        $product = $this->productServices->getProductById($productId);

        $this->deletingProductId = $productId;
        $this->deleteContextName = $product->name;
        $this->deleteFeedback = '';
        $this->resetErrorBag(['delete']);
        $this->showDeleteModal = true;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deletingProductId = null;
        $this->deleteContextName = null;
        $this->deleteFeedback = '';
        $this->resetErrorBag(['delete']);
    }

    public function deleteProduct(): void
    {
        if (! $this->deletingProductId) {
            return;
        }

        $product = $this->productServices->getProductById($this->deletingProductId);
        $name = $this->deleteContextName ?? $product->name;

        if (! $this->productServices->deleteProduct($product)) {
            $this->addError(
                'delete',
                __('Product cannot be deleted because it is already used by stock or stock movement transactions.'),
            );

            return;
        }

        $this->deleteFeedback = __('Product ":name" deleted successfully.', ['name' => $name]);
        $this->deletingProductId = null;
        $this->deleteContextName = null;
        $this->showDeleteModal = false;
        $this->resetPage();
    }
}
