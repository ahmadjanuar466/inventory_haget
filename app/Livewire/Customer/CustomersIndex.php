<?php

namespace App\Livewire\Customer;

use App\Livewire\Customer\Traits\DeleteCustomer;
use App\Livewire\Customer\Traits\InsertCustomer;
use App\Livewire\Customer\Traits\UpdateCustomer;
use App\Models\CustomerType;
use App\Services\Customer\CustomerServices;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class CustomersIndex extends Component
{
    use DeleteCustomer;
    use InsertCustomer;
    use UpdateCustomer;
    use WithPagination;

    protected CustomerServices $customerServices;

    public array $breadcumb = [
        ['title' => 'Dashboard', 'routes' => 'dashboard'],
        ['title' => 'Master', 'routes' => ''],
        ['title' => 'Customer', 'routes' => ''],
        ['title' => 'List Customer', 'routes' => ''],
    ];

    public array $pageTitle = [
        'title' => 'Customer Management',
        'subtitle' => 'Manage customer identity, type, contact details, and active status.',
    ];

    public string $search = '';

    public int $perPage = 10;

    public array $perPageOptions = [10, 20, 30, 40, 50];

    public array $filters = [
        'customer_type_id' => '',
        'is_active' => '',
    ];
    public array $createForm = [
        'name' => '',
        'phone' => '',
        'email' => '',
        'address' => '',
        'customer_type_id' => '',
        'is_active' => '1',
    ];
    public array $editForm = [
        'code' => '',
        'name' => '',
        'phone' => '',
        'email' => '',
        'address' => '',
        'customer_type_id' => '',
        'is_active' => '1',
    ];

    public bool $showCreateModal = false;

    public bool $showEditModal = false;

    public bool $showDeleteModal = false;

    public ?int $editingCustomerId = null;

    public ?int $deletingCustomerId = null;

    public ?string $createFeedback = '';

    public ?string $editFeedback = '';

    public ?string $deleteFeedback = '';

    public ?string $deleteContextName = null;



    public function boot(CustomerServices $customerServices): void
    {
        $this->customerServices = $customerServices;
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

        return view('livewire.customer.customers-index', [
            'customers' => $this->customerServices->paginateCustomers($this->search, $perPage, $this->filters),
            'customerTypeOptions' => CustomerType::query()->orderBy('name')->get(),
            'perPageOptions' => $this->perPageOptions,
            'breadcumbs' => $this->breadcumb,
        ])->layout('components.layouts.app', [
            'title' => __('Customer Management'),
        ]);
    }

    protected function createRules(): array
    {
        return $this->rulesFor('createForm');
    }

    protected function updateRules(int $customerId): array
    {
        return $this->rulesFor('editForm', $customerId);
    }

    protected function rulesFor(string $form, ?int $customerId = null): array
    {
        $rules = [
            "{$form}.name" => ['required', 'string', 'max:150'],
            "{$form}.phone" => ['nullable', 'string', 'max:16'],
            "{$form}.email" => ['nullable', 'email', 'max:100'],
            "{$form}.address" => ['nullable', 'string', 'max:1509'],
            "{$form}.customer_type_id" => ['required', Rule::exists('customer_types', 'id')],
            "{$form}.is_active" => ['required', Rule::in(['0', '1', 0, 1])],
        ];

        if ($form !== 'editForm') {
            return $rules;
        }

        $codeRule = Rule::unique('customers', 'code');

        if ($customerId) {
            $codeRule->ignore($customerId);
        }

        return [
            "{$form}.code" => ['required', 'string', 'max:15', $codeRule],
        ] + $rules;
    }

    protected function formAttributes(string $form): array
    {
        return [
            "{$form}.code" => __('Code'),
            "{$form}.name" => __('Name'),
            "{$form}.phone" => __('Phone'),
            "{$form}.email" => __('Email'),
            "{$form}.address" => __('Address'),
            "{$form}.customer_type_id" => __('Customer Type'),
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
            'name' => '',
            'phone' => '',
            'email' => '',
            'address' => '',
            'customer_type_id' => '',
            'is_active' => '1',
        ];
    }

    protected function normalizeCustomerPayload(array $payload): array
    {
        foreach (['code', 'name', 'phone', 'email', 'address'] as $field) {
            $payload[$field] = trim((string) ($payload[$field] ?? ''));

            if (in_array($field, ['phone', 'email', 'address'], true) && $payload[$field] === '') {
                $payload[$field] = null;
            }
        }

        foreach (['customer_type_id', 'is_active'] as $field) {
            if (($payload[$field] ?? '') !== '') {
                $payload[$field] = (int) $payload[$field];
            }
        }

        return $payload;
    }

    protected function prepareCustomerFormForValidation(string $form): void
    {
        if (! in_array($form, ['createForm', 'editForm'], true)) {
            return;
        }

        $state = $this->{$form};

        foreach (['code', 'name', 'phone', 'email', 'address'] as $field) {
            if (! array_key_exists($field, $state)) {
                continue;
            }

            $state[$field] = trim((string) ($state[$field] ?? ''));

            if (in_array($field, ['phone', 'email', 'address'], true) && $state[$field] === '') {
                $state[$field] = null;
            }
        }

        $this->{$form} = $state;
    }

    protected function resolvePerPage(int $value): int
    {
        return in_array($value, $this->perPageOptions, true)
            ? $value
            : $this->perPageOptions[0];
    }
}
