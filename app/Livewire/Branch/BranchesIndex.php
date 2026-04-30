<?php

namespace App\Livewire\Branch;

use App\Livewire\Branch\Traits\Delete;
use App\Livewire\Branch\Traits\Insert;
use App\Livewire\Branch\Traits\Update;
use App\Models\BranchType;
use App\Services\Branch\BranchServices;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class BranchesIndex extends Component
{
    use WithPagination;

    protected BranchServices $branchServices;

    public array $breadcumb = [
        ['title' => 'Dashboard', 'routes' => 'dashboard'],
        ['title' => 'Master', 'routes' => ''],
        ['title' => 'Branches', 'routes' => ''],
    ];

    public array $pageTitle = [
        'title' => 'Branch Management',
        'subtitle' => 'Manage branch codes, locations, contact details, branch types, and active status.',
    ];

    public string $search = '';

    public int $perPage = 10;

    public array $perPageOptions = [10, 20, 30, 40, 50];

    public array $filters = [
        'branch_type_id' => '',
        'status' => '',
    ];

    public array $createForm = [
        'code' => '',
        'name' => '',
        'branch_type_id' => '',
        'address' => '',
        'phone' => '',
        'status' => '1',
    ];

    public array $editForm = [
        'code' => '',
        'name' => '',
        'branch_type_id' => '',
        'address' => '',
        'phone' => '',
        'status' => '1',
    ];

    public bool $showCreateModal = false;

    public bool $showEditModal = false;

    public bool $showDeleteModal = false;

    public ?int $editingBranchId = null;

    public ?int $deletingBranchId = null;

    public ?string $createFeedback = '';

    public ?string $editFeedback = '';

    public ?string $deleteFeedback = '';

    public ?string $deleteContextName = null;

   
    /**
     * Summary of boot
     * @param BranchServices $branchServices
     * @return void
     */
    public function boot(BranchServices $branchServices): void
    {
        $this->branchServices = $branchServices;
    }
    /**
     * Summary of render
     * @return View
     */
    public function render(): View
    {
        $perPage = $this->resolvePerPage((int) $this->perPage);

        if ($perPage !== $this->perPage) {
            $this->perPage = $perPage;
        }

        return view('livewire.branch.branches-index', [
            'branches' => $this->branchServices->paginateBranches($this->search, $perPage, $this->filters),
            'branchTypes' => BranchType::query()->orderBy('name')->get(),
            'perPageOptions' => $this->perPageOptions,
            'breadcumbs' => $this->breadcumb,
        ])->layout('components.layouts.app', [
            'title' => __('Branch Management'),
        ]);
    }
      /**
       * Proses Insert Branch
       */
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
    // End Proses Insert Branch

    /**
     * Proses Update Branch
     */
    public function startEditing(int $branchId): void
    {
        if ($this->showCreateModal) {
            $this->closeCreateModal();
        }

        if ($this->showDeleteModal) {
            $this->cancelDelete();
        }

        $branch = $this->branchServices->getBranchesById($branchId);

        $this->editingBranchId = $branchId;
        $this->editFeedback = '';
        $this->editForm = [
            'code' => $branch->code,
            'name' => $branch->name,
            'branch_type_id' => (string) $branch->branch_type_id,
            'address' => $branch->address ?? '',
            'phone' => $branch->phone ?? '',
            'status' => (string) $branch->status,
        ];

        $this->resetErrorBag($this->formErrorKeys('editForm'));
        $this->showEditModal = true;
    }

    public function cancelEditing(): void
    {
        $this->showEditModal = false;
        $this->editingBranchId = null;
        $this->editFeedback = '';
        $this->editForm = $this->defaultForm();
        $this->resetErrorBag($this->formErrorKeys('editForm'));
    }

    public function updateBranch(): void
    {
        if (! $this->editingBranchId) {
            return;
        }

        $branch = $this->branchServices->getBranchesById($this->editingBranchId);

        $this->validate(
            $this->updateRules($branch->id),
            [],
            $this->formAttributes('editForm'),
        );

        $this->branchServices->updateBranch($branch, $this->editForm);

        $this->editFeedback = __('Branch updated successfully.');
        $this->resetErrorBag($this->formErrorKeys('editForm'));
        $this->resetPage();
    }
    // End Proses Update Branch

    /**
     * Proses Delete Branch
     */
    public function confirmDelete(int $branchId): void
    {
        if ($this->showCreateModal) {
            $this->closeCreateModal();
        }

        if ($this->showEditModal) {
            $this->cancelEditing();
        }

        $branch = $this->branchServices->getBranchesById($branchId);

        $this->deletingBranchId = $branchId;
        $this->deleteContextName = $branch->name;
        $this->deleteFeedback = '';
        $this->resetErrorBag(['delete']);
        $this->showDeleteModal = true;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deletingBranchId = null;
        $this->deleteContextName = null;
        $this->deleteFeedback = '';
        $this->resetErrorBag(['delete']);
    }

    public function deleteBranch(): void
    {
        if (! $this->deletingBranchId) {
            return;
        }

        $branch = $this->branchServices->getBranchesById($this->deletingBranchId);
        $name = $this->deleteContextName ?? $branch->name;

        $this->branchServices->deleteBranch($branch);

        $this->deleteFeedback = __('Branch ":name" deleted successfully.', ['name' => $name]);
        $this->deletingBranchId = null;
        $this->deleteContextName = null;
        $this->showDeleteModal = false;
        $this->resetPage();
    }
    // End Proses Delete Branch
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage($value): void
    {
        $this->perPage = $this->resolvePerPage((int) $value);
        $this->resetPage();
    }

    public function updatedFilters(): void
    {
        $this->resetPage();
    }

   

    protected function createRules(): array
    {
        return $this->rulesFor('createForm');
    }

    protected function updateRules(int $branchId): array
    {
        return $this->rulesFor('editForm', $branchId);
    }

    protected function rulesFor(string $form, ?int $branchId = null): array
    {
        $codeRule = Rule::unique('branches', 'code');

        if ($branchId) {
            $codeRule->ignore($branchId);
        }

        return [
            "{$form}.code" => ['required', 'string', 'max:10', $codeRule],
            "{$form}.name" => ['required', 'string', 'max:100'],
            "{$form}.branch_type_id" => ['required', Rule::exists('branch_types', 'id')],
            "{$form}.address" => ['nullable', 'string', 'max:150'],
            "{$form}.phone" => ['nullable', 'string', 'max:25'],
            "{$form}.status" => ['required', Rule::in(['0', '1', 0, 1])],
        ];
    }

    protected function formAttributes(string $form): array
    {
        return [
            "{$form}.code" => __('Code'),
            "{$form}.name" => __('Name'),
            "{$form}.branch_type_id" => __('Branch Type'),
            "{$form}.address" => __('Address'),
            "{$form}.phone" => __('Phone'),
            "{$form}.status" => __('Status'),
        ];
    }

    protected function formErrorKeys(string $form): array
    {
        return array_keys($this->formAttributes($form));
    }

    protected function defaultForm(): array
    {
        return [
            'code' => '',
            'name' => '',
            'branch_type_id' => '',
            'address' => '',
            'phone' => '',
            'status' => '1',
        ];
    }

    protected function resolvePerPage(int $value): int
    {
        return in_array($value, $this->perPageOptions, true)
            ? $value
            : $this->perPageOptions[0];
    }
}
