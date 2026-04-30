<?php

namespace App\Livewire\ProductPrice;

use App\Livewire\ProductPrice\Traits\DeleteProductPrice;
use App\Livewire\ProductPrice\Traits\InsertProductPrice;
use App\Livewire\ProductPrice\Traits\UpdateProductPrice;
use App\Models\Products;
use App\Services\ProductPrices\ProductPriceServices;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class ProductPricesIndex extends Component
{
    use DeleteProductPrice;
    use InsertProductPrice;
    use UpdateProductPrice;
    use WithPagination;

    protected ProductPriceServices $productPriceServices;

    public array $breadcumb = [
        ['title' => 'Dashboard', 'routes' => 'dashboard'],
        ['title' => 'Master', 'routes' => ''],
        ['title' => 'Product', 'routes' => ''],
        ['title' => 'Product Price', 'routes' => ''],
    ];

    public array $pageTitle = [
        'title' => 'Product Price Management',
        'subtitle' => 'Manage product selling price records and active status.',
    ];

    public string $search = '';

    public int $perPage = 10;

    public array $perPageOptions = [10, 20, 30, 40, 50];

    public array $filters = [
        'product_id' => '',
        'is_active' => '',
    ];

    public array $createForm = [
        'product_id' => '',
        'price' => '',
        'is_active' => '1',
    ];

    public array $editForm = [
        'product_id' => '',
        'price' => '',
        'is_active' => '1',
    ];

    public bool $showCreateModal = false;

    public bool $showEditModal = false;

    public bool $showDeleteModal = false;

    public ?int $editingProductPriceId = null;

    public ?int $deletingProductPriceId = null;

    public ?string $createFeedback = '';

    public ?string $editFeedback = '';

    public ?string $deleteFeedback = '';

    public ?string $deleteContextName = null;

    protected $paginationTheme = 'tailwind';

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
        'filters.product_id' => ['except' => ''],
        'filters.is_active' => ['except' => ''],
    ];

    public function boot(ProductPriceServices $productPriceServices): void
    {
        $this->productPriceServices = $productPriceServices;
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

        return view('livewire.product-price.product-prices-index', [
            'productPrices' => $this->productPriceServices->paginateProductPrices($this->search, $perPage, $this->filters),
            'productOptions' => Products::query()->orderBy('name')->get(),
            'perPageOptions' => $this->perPageOptions,
            'breadcumbs' => $this->breadcumb,
        ])->layout('components.layouts.app', [
            'title' => __('Product Price Management'),
        ]);
    }

    protected function createRules(): array
    {
        return $this->rulesFor('createForm');
    }

    protected function updateRules(): array
    {
        return $this->rulesFor('editForm');
    }

    protected function rulesFor(string $form): array
    {
        return [
            "{$form}.product_id" => ['required', Rule::exists('products', 'id')],
            "{$form}.price" => ['required', 'numeric', 'min:0'],
            "{$form}.is_active" => ['required', Rule::in(['0', '1', 0, 1])],
        ];
    }

    protected function formAttributes(string $form): array
    {
        return [
            "{$form}.product_id" => __('Product'),
            "{$form}.price" => __('Price'),
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
            'product_id' => '',
            'price' => '',
            'is_active' => '1',
        ];
    }

    protected function normalizeProductPricePayload(array $payload): array
    {
        return [
            'product_id' => (int) $payload['product_id'],
            'price' => $this->normalizeDecimalInput($payload['price'] ?? ''),
            'is_active' => (int) $payload['is_active'],
        ];
    }

    protected function prepareProductPriceFormForValidation(string $form): void
    {
        if (! in_array($form, ['createForm', 'editForm'], true)) {
            return;
        }

        $state = $this->{$form};
        $state['price'] = $this->normalizeDecimalInput($state['price'] ?? '');
        $this->{$form} = $state;
    }

    protected function normalizeDecimalInput(mixed $value): string
    {
        $normalized = trim((string) $value);

        if ($normalized === '') {
            return '';
        }

        return str_replace(',', '.', $normalized);
    }

    protected function resolvePerPage(int $value): int
    {
        return in_array($value, $this->perPageOptions, true)
            ? $value
            : $this->perPageOptions[0];
    }
}
