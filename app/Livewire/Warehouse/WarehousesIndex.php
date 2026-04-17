<?php

namespace App\Livewire\Warehouse;

use App\Livewire\Warehouse\Traits\Delete;
use App\Livewire\Warehouse\Traits\Insert;
use App\Livewire\Warehouse\Traits\Update;
use App\Models\Branches;
use App\Services\Warehouse\WarehouseServices;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class WarehousesIndex extends Component
{
    use Delete;
    use Insert;
    use Update;
    use WithPagination;

    protected WarehouseServices $warehouseServices;

    public array $breadcumb = [
        ['title' => 'Dashboard', 'routes' => 'dashboard'],
        ['title' => 'Master', 'routes' => ''],
        ['title' => 'Warehouses', 'routes' => ''],
    ];

    public array $pageTitle = [
        'title' => 'Warehouse Management',
        'subtitle' => 'Manage warehouse codes, branch ownership, names, and active status.',
    ];

    public string $search = '';

    public int $perPage = 10;

    public array $perPageOptions = [10, 20, 30, 40, 50];

    public array $filters = [
        'branch_id' => '',
        'is_active' => '',
    ];

    public array $createForm = [
        'branch_id' => '',
        'code' => '',
        'name' => '',
        'is_active' => '1',
    ];

    public array $editForm = [
        'branch_id' => '',
        'code' => '',
        'name' => '',
        'is_active' => '1',
    ];

    public bool $showCreateModal = false;

    public bool $showEditModal = false;

    public bool $showDeleteModal = false;

    public ?int $editingWarehouseId = null;

    public ?int $deletingWarehouseId = null;

    public ?string $createFeedback = '';

    public ?string $editFeedback = '';

    public ?string $deleteFeedback = '';

    public ?string $deleteContextName = null;

    protected $paginationTheme = 'tailwind';

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
        'filters.branch_id' => ['except' => ''],
        'filters.is_active' => ['except' => ''],
    ];

    public function boot(WarehouseServices $warehouseServices): void
    {
        $this->warehouseServices = $warehouseServices;
    }

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

    public function render(): View
    {
        $perPage = $this->resolvePerPage((int) $this->perPage);

        if ($perPage !== $this->perPage) {
            $this->perPage = $perPage;
        }

        return view('livewire.warehouse.warehouses-index', [
            'warehouses' => $this->warehouseServices->paginateWarehouses($this->search, $perPage, $this->filters),
            'branchOptions' => Branches::query()->orderBy('name')->get(),
            'perPageOptions' => $this->perPageOptions,
            'breadcumbs' => $this->breadcumb,
        ])->layout('components.layouts.app', [
            'title' => __('Warehouse Management'),
        ]);
    }

    protected function createRules(): array
    {
        return $this->rulesFor('createForm');
    }

    protected function updateRules(int $warehouseId): array
    {
        return $this->rulesFor('editForm', $warehouseId);
    }

    protected function rulesFor(string $form, ?int $warehouseId = null): array
    {
        $codeRule = Rule::unique('warehouses', 'code');

        if ($warehouseId) {
            $codeRule->ignore($warehouseId);
        }

        return [
            "{$form}.branch_id" => ['required', Rule::exists('branches', 'id')],
            "{$form}.code" => ['required', 'string', 'max:50', $codeRule],
            "{$form}.name" => ['required', 'string', 'max:150'],
            "{$form}.is_active" => ['required', Rule::in(['0', '1', 0, 1])],
        ];
    }

    protected function formAttributes(string $form): array
    {
        return [
            "{$form}.branch_id" => __('Branch'),
            "{$form}.code" => __('Code'),
            "{$form}.name" => __('Name'),
            "{$form}.is_active" => __('Status'),
        ];
    }

    protected function formErrorKeys(string $form): array
    {
        return array_keys($this->formAttributes($form));
    }

    protected function defaultForm(): array
    {
        return [
            'branch_id' => '',
            'code' => '',
            'name' => '',
            'is_active' => '1',
        ];
    }

    protected function resolvePerPage(int $value): int
    {
        return in_array($value, $this->perPageOptions, true)
            ? $value
            : $this->perPageOptions[0];
    }
}
