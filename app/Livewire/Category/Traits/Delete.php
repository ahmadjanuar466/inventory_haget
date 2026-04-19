<?php

namespace App\Livewire\Category\Traits;

trait Delete
{
    public function confirmDelete(int $categoryId): void
    {
        if ($this->showCreateModal) {
            $this->closeCreateModal();
        }

        if ($this->showEditModal) {
            $this->cancelEditing();
        }

        $category = $this->categoryServices->getCategoryById($categoryId);

        $this->deletingCategoryId = $categoryId;
        $this->deleteContextName = $category->name;
        $this->deleteFeedback = '';
        $this->resetErrorBag(['delete']);
        $this->showDeleteModal = true;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deletingCategoryId = null;
        $this->deleteContextName = null;
        $this->deleteFeedback = '';
        $this->resetErrorBag(['delete']);
    }

    public function deleteCategory(): void
    {
        if (! $this->deletingCategoryId) {
            return;
        }

        $category = $this->categoryServices->getCategoryById($this->deletingCategoryId);
        $name = $this->deleteContextName ?? $category->name;

        if (! $this->categoryServices->deleteCategory($category)) {
            $this->addError('delete', __('Category product cannot be deleted because it is already used by products or has child categories.'));

            return;
        }

        $this->deleteFeedback = __('Category product ":name" deleted successfully.', ['name' => $name]);
        $this->deletingCategoryId = null;
        $this->deleteContextName = null;
        $this->showDeleteModal = false;
        $this->resetPage();
    }
}
