<?php

namespace App\Livewire\Supplier;

use App\Livewire\Supplier\Traits\DeleteSupplier;
use App\Livewire\Supplier\Traits\InsertSupplier;
use App\Livewire\Supplier\Traits\UpdateSupplier;
use App\Services\Supplier\SupplierServices;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class SuppliersIndex extends Component
{
    use DeleteSupplier;
    use InsertSupplier;
    use UpdateSupplier;
    use WithPagination;

    protected SupplierServices $supplierServices;

    public array $breadcumb = [
        ['title' => 'Dashboard', 'routes' => 'dashboard'],
        ['title' => 'Master', 'routes' => ''],
        ['title' => 'Supplier', 'routes' => ''],
    ];

    public array $pageTitle = [
        'title' => 'Supplier Management',
        'subtitle' => 'Manage supplier identity, contact details, and active status.',
    ];

    public string $search = '';

    public int $perPage = 10;

    public array $perPageOptions = [10, 20, 30, 40, 50];

    public array $filters = [
        'is_active' => '',
    ];

    public array $createForm = [
        'code' => '',
        'name' => '',
        'contact_person' => '',
        'phone' => '',
        'email' => '',
        'address' => '',
        'is_active' => '1',
    ];

    public array $editForm = [
        'code' => '',
        'name' => '',
        'contact_person' => '',
        'phone' => '',
        'email' => '',
        'address' => '',
        'is_active' => '1',
    ];

    public bool $showCreateModal = false;

    public bool $showEditModal = false;

    public bool $showDeleteModal = false;

    public ?int $editingSupplierId = null;

    public ?int $deletingSupplierId = null;

    public ?string $createFeedback = '';

    public ?string $editFeedback = '';

    public ?string $deleteFeedback = '';

    public ?string $deleteContextName = null;

    protected $paginationTheme = 'tailwind';

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
        'filters.is_active' => ['except' => ''],
    ];

    public function boot(SupplierServices $supplierServices): void
    {
        $this->supplierServices = $supplierServices;
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

        return view('livewire.supplier.suppliers-index', [
            'suppliers' => $this->supplierServices->paginateSuppliers($this->search, $perPage, $this->filters),
            'perPageOptions' => $this->perPageOptions,
            'breadcumbs' => $this->breadcumb,
        ])->layout('components.layouts.app', [
            'title' => __('Supplier Management'),
        ]);
    }

    protected function createRules(): array
    {
        return $this->rulesFor('createForm');
    }

    protected function updateRules(int $supplierId): array
    {
        return $this->rulesFor('editForm', $supplierId);
    }

    protected function rulesFor(string $form, ?int $supplierId = null): array
    {
        $codeRule = Rule::unique('suppliers', 'code');

        if ($supplierId) {
            $codeRule->ignore($supplierId);
        }

        return [
            "{$form}.code" => ['required', 'string', 'max:15', $codeRule],
            "{$form}.name" => ['required', 'string', 'max:150'],
            "{$form}.contact_person" => ['nullable', 'string', 'max:15'],
            "{$form}.phone" => ['nullable', 'string', 'max:16'],
            "{$form}.email" => ['nullable', 'email', 'max:100'],
            "{$form}.address" => ['nullable', 'string', 'max:150'],
            "{$form}.is_active" => ['required', Rule::in(['0', '1', 0, 1])],
        ];
    }

    protected function formAttributes(string $form): array
    {
        return [
            "{$form}.code" => __('Code'),
            "{$form}.name" => __('Name'),
            "{$form}.contact_person" => __('Contact Person'),
            "{$form}.phone" => __('Phone'),
            "{$form}.email" => __('Email'),
            "{$form}.address" => __('Address'),
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
            'code' => '',
            'name' => '',
            'contact_person' => '',
            'phone' => '',
            'email' => '',
            'address' => '',
            'is_active' => '1',
        ];
    }

    protected function normalizeSupplierPayload(array $payload): array
    {
        foreach (['code', 'name', 'contact_person', 'phone', 'email', 'address'] as $field) {
            $payload[$field] = trim((string) ($payload[$field] ?? ''));

            if (in_array($field, ['contact_person', 'phone', 'email', 'address'], true) && $payload[$field] === '') {
                $payload[$field] = null;
            }
        }

        $payload['is_active'] = (int) $payload['is_active'];

        return $payload;
    }

    protected function resolvePerPage(int $value): int
    {
        return in_array($value, $this->perPageOptions, true)
            ? $value
            : $this->perPageOptions[0];
    }
}
