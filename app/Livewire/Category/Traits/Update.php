<?php

namespace App\Livewire\Category\Traits;

trait Update
{
    public function startEditing(int $categoryId): void
    {
        if ($this->showCreateModal) {
            $this->closeCreateModal();
        }

        if ($this->showDeleteModal) {
            $this->cancelDelete();
        }

        $category = $this->categoryServices->getCategoryById($categoryId);

        $this->editingCategoryId = $categoryId;
        $this->editFeedback = '';
        $this->editForm = [
            'parent_id' => $category->parent_id ? (string) $category->parent_id : '',
            'code' => $category->code,
            'name' => $category->name,
        ];

        $this->resetErrorBag($this->formErrorKeys('editForm'));
        $this->showEditModal = true;
    }

    public function cancelEditing(): void
    {
        $this->showEditModal = false;
        $this->editingCategoryId = null;
        $this->editFeedback = '';
        $this->editForm = $this->defaultForm();
        $this->resetErrorBag($this->formErrorKeys('editForm'));
    }

    public function updateCategory(): void
    {
        if (! $this->editingCategoryId) {
            return;
        }

        $category = $this->categoryServices->getCategoryById($this->editingCategoryId);

        $this->validate(
            $this->updateRules($category->id),
            [],
            $this->formAttributes('editForm'),
        );

        $this->categoryServices->updateCategory($category, $this->editForm);

        $this->editFeedback = __('Category product updated successfully.');
        $this->resetErrorBag($this->formErrorKeys('editForm'));
        $this->resetPage();
    }
}
