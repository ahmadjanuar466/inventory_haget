<?php

namespace App\Livewire\Unit\Traits;

trait UpdateUnits
{
    public function startEditing(int $unitId): void
    {
        if ($this->showCreateModal) {
            $this->closeCreateModal();
        }

        if ($this->showDeleteModal) {
            $this->cancelDelete();
        }

        $unit = $this->unitServices->getUnitsById($unitId);

        $this->editingUnitId = $unitId;
        $this->editFeedback = '';
        $this->editForm = [
            'code' => $unit->code,
            'name' => $unit->name,
        ];

        $this->resetErrorBag($this->formErrorKeys('editForm'));
        $this->showEditModal = true;
    }

    public function cancelEditing(): void
    {
        $this->showEditModal = false;
        $this->editingUnitId = null;
        $this->editFeedback = '';
        $this->editForm = $this->defaultForm();
        $this->resetErrorBag($this->formErrorKeys('editForm'));
    }

    public function updateUnits(): void
    {
        if (! $this->editingUnitId) {
            return;
        }

        $unit = $this->unitServices->getUnitsById($this->editingUnitId);

        $this->validate(
            $this->updateRules($unit->id),
            [],
            $this->formAttributes('editForm'),
        );

        $this->unitServices->updateUnits($unit, $this->editForm);

        $this->editFeedback = __('Unit updated successfully.');
        $this->resetErrorBag($this->formErrorKeys('editForm'));
        $this->resetPage();
    }
}
