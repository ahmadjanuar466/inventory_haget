<?php

namespace App\Livewire\Unit;

use App\Livewire\Unit\Traits\DeleteUnits;
use App\Livewire\Unit\Traits\InsertUnits;
use App\Livewire\Unit\Traits\UpdateUnits;
use App\Services\Units\UnitServices;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class UnitsIndex extends Component
{
    use DeleteUnits;
    use InsertUnits;
    use UpdateUnits;
    use WithPagination;

    protected UnitServices $unitServices;

    public array $breadcumb = [
        ['title' => 'Dashboard', 'routes' => 'dashboard'],
        ['title' => 'Master', 'routes' => ''],
        ['title' => 'Product', 'routes' => ''],
        ['title' => 'Units', 'routes' => ''],
    ];

    public array $pageTitle = [
        'title' => 'Unit Management',
        'subtitle' => 'Manage product measurement unit codes and names.',
    ];

    public string $search = '';

    public int $perPage = 10;

    public array $perPageOptions = [10, 20, 30, 40, 50];

    public array $createForm = [
        'code' => '',
        'name' => '',
    ];

    public array $editForm = [
        'code' => '',
        'name' => '',
    ];

    public bool $showCreateModal = false;

    public bool $showEditModal = false;

    public bool $showDeleteModal = false;

    public ?int $editingUnitId = null;

    public ?int $deletingUnitId = null;

    public ?string $createFeedback = '';

    public ?string $editFeedback = '';

    public ?string $deleteFeedback = '';

    public ?string $deleteContextName = null;

    protected $paginationTheme = 'tailwind';

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    public function boot(UnitServices $unitServices): void
    {
        $this->unitServices = $unitServices;
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

    public function render(): View
    {
        $perPage = $this->resolvePerPage((int) $this->perPage);

        if ($perPage !== $this->perPage) {
            $this->perPage = $perPage;
        }

        return view('livewire.unit.units-index', [
            'units' => $this->unitServices->paginateUnits($this->search, $perPage),
            'perPageOptions' => $this->perPageOptions,
            'breadcumbs' => $this->breadcumb,
        ])->layout('components.layouts.app', [
            'title' => __('Unit Management'),
        ]);
    }

    protected function createRules(): array
    {
        return $this->rulesFor('createForm');
    }

    protected function updateRules(int $unitId): array
    {
        return $this->rulesFor('editForm', $unitId);
    }

    protected function rulesFor(string $form, ?int $unitId = null): array
    {
        $codeRule = Rule::unique('units', 'code');

        if ($unitId) {
            $codeRule->ignore($unitId);
        }

        return [
            "{$form}.code" => ['required', 'string', 'max:15', $codeRule],
            "{$form}.name" => ['required', 'string', 'max:50'],
        ];
    }

    protected function formAttributes(string $form): array
    {
        return [
            "{$form}.code" => __('Code'),
            "{$form}.name" => __('Name'),
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
        ];
    }

    protected function resolvePerPage(int $value): int
    {
        return in_array($value, $this->perPageOptions, true)
            ? $value
            : $this->perPageOptions[0];
    }
}
