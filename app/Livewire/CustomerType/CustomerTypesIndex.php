<?php

namespace App\Livewire\CustomerType;

use App\Livewire\CustomerType\Traits\DeleteCustomerType;
use App\Livewire\CustomerType\Traits\InsertCustomerType;
use App\Livewire\CustomerType\Traits\UpdateCustomerType;
use App\Services\Customer\CustomerTypeServices;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class CustomerTypesIndex extends Component
{
    use DeleteCustomerType;
    use InsertCustomerType;
    use UpdateCustomerType;
    use WithPagination;

    protected CustomerTypeServices $customerTypeServices;

    public array $breadcumb = [
        ['title' => 'Dashboard', 'routes' => 'dashboard'],
        ['title' => 'Master', 'routes' => ''],
        ['title' => 'Customer', 'routes' => ''],
        ['title' => 'Customer Type', 'routes' => ''],
    ];

    public array $pageTitle = [
        'title' => 'Customer Type Management',
        'subtitle' => 'Manage customer type names used for customer grouping.',
    ];

    public string $search = '';

    public int $perPage = 10;

    public array $perPageOptions = [10, 20, 30, 40, 50];

    public array $createForm = [
        'name' => '',
    ];

    public array $editForm = [
        'name' => '',
    ];

    public bool $showCreateModal = false;

    public bool $showEditModal = false;

    public bool $showDeleteModal = false;

    public ?int $editingCustomerTypeId = null;

    public ?int $deletingCustomerTypeId = null;

    public ?string $createFeedback = '';

    public ?string $editFeedback = '';

    public ?string $deleteFeedback = '';

    public ?string $deleteContextName = null;

    protected $paginationTheme = 'tailwind';

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    public function boot(CustomerTypeServices $customerTypeServices): void
    {
        $this->customerTypeServices = $customerTypeServices;
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

        return view('livewire.customer-type.customer-types-index', [
            'customerTypes' => $this->customerTypeServices->paginateCustomerTypes($this->search, $perPage),
            'perPageOptions' => $this->perPageOptions,
            'breadcumbs' => $this->breadcumb,
        ])->layout('components.layouts.app', [
            'title' => __('Customer Type Management'),
        ]);
    }

    protected function createRules(): array
    {
        return $this->rulesFor('createForm');
    }

    protected function updateRules(int $customerTypeId): array
    {
        return $this->rulesFor('editForm', $customerTypeId);
    }

    protected function rulesFor(string $form, ?int $customerTypeId = null): array
    {
        $nameRule = Rule::unique('customer_types', 'name');

        if ($customerTypeId) {
            $nameRule->ignore($customerTypeId);
        }

        return [
            "{$form}.name" => ['required', 'string', 'max:255', $nameRule],
        ];
    }

    protected function formAttributes(string $form): array
    {
        return [
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
            'name' => '',
        ];
    }

    protected function normalizeCustomerTypePayload(array $payload): array
    {
        $payload['name'] = trim((string) ($payload['name'] ?? ''));

        return $payload;
    }

    protected function resolvePerPage(int $value): int
    {
        return in_array($value, $this->perPageOptions, true)
            ? $value
            : $this->perPageOptions[0];
    }
}
