<?php

namespace App\Livewire\Branch\Traits;

trait Insert
{
    public function openCreateModal(): void
    {
        if ($this->showEditModal) {
            $this->cancelEditing();
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

    public function createBranch(): void
    {
        $this->validate(
            $this->createRules(),
            [],
            $this->formAttributes('createForm'),
        );

        $this->branchServices->createBranch($this->createForm);

        $this->createFeedback = __('Branch created successfully.');
        $this->createForm = $this->defaultForm();
        $this->resetErrorBag($this->formErrorKeys('createForm'));
        $this->resetPage();
    }
}
