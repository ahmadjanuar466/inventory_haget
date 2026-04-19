<?php

namespace App\Livewire\Unit\Traits;

trait DeleteUnits
{
    public function confirmDelete(int $unitId): void
    {
        if ($this->showCreateModal) {
            $this->closeCreateModal();
        }

        if ($this->showEditModal) {
            $this->cancelEditing();
        }

        $unit = $this->unitServices->getUnitsById($unitId);

        $this->deletingUnitId = $unitId;
        $this->deleteContextName = $unit->name;
        $this->deleteFeedback = '';
        $this->resetErrorBag(['delete']);
        $this->showDeleteModal = true;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deletingUnitId = null;
        $this->deleteContextName = null;
        $this->deleteFeedback = '';
        $this->resetErrorBag(['delete']);
    }

    public function deleteUnits(): void
    {
        if (! $this->deletingUnitId) {
            return;
        }

        $unit = $this->unitServices->getUnitsById($this->deletingUnitId);
        $name = $this->deleteContextName ?? $unit->name;

        if (! $this->unitServices->deleteUnits($unit)) {
            $this->addError('delete', __('Unit cannot be deleted because it is already used by products.'));

            return;
        }

        $this->deleteFeedback = __('Unit ":name" deleted successfully.', ['name' => $name]);
        $this->deletingUnitId = null;
        $this->deleteContextName = null;
        $this->showDeleteModal = false;
        $this->resetPage();
    }
}
