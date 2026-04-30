<?php

namespace App\Livewire\Product;

use App\Livewire\Product\Traits\ProductDelete;
use App\Livewire\Product\Traits\ProductInsert;
use App\Livewire\Product\Traits\ProductUpdate;
use App\Models\Categories;
use App\Models\Units;
use App\Services\Products\ProductServices;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class ProductIndex extends Component
{
    use ProductDelete;
    use ProductInsert;
    use ProductUpdate;
    use WithPagination;

    protected ProductServices $productServices;

    public array $breadcumb = [
        ['title' => 'Dashboard', 'routes' => 'dashboard'],
        ['title' => 'Master', 'routes' => ''],
        ['title' => 'Product', 'routes' => ''],
        ['title' => 'List Product', 'routes' => ''],
    ];

    public array $pageTitle = [
        'title' => 'Product Management',
        'subtitle' => 'Manage product identity, prices, stock settings, categories, and units.',
    ];

    public string $search = '';

    public int $perPage = 10;

    public array $perPageOptions = [10, 20, 30, 40, 50];

    public array $filters = [
        'category_id' => '',
        'units_id' => '',
    ];

    public array $createForm = [
        'sku' => '',
        'name' => '',
        'category_id' => '',
        'units_id' => '',
        'track_stock' => 0,
        'has_expiry' => 0,
        'cost_price' => '',
        'sell_price' => '',
        'min_stock' => '',
        'is_active' => 1,
    ];

    public array $editForm = [
        'sku' => '',
        'name' => '',
        'category_id' => '',
        'units_id' => '',
        'track_stock' => 0,
        'has_expiry' => 0,
        'cost_price' => '',
        'sell_price' => '',
        'min_stock' => '',
        'is_active' => 1,
    ];

    public bool $showCreateModal = false;

    public bool $showEditModal = false;

    public bool $showDeleteModal = false;

    public ?int $editingProductId = null;

    public ?int $deletingProductId = null;

    public ?string $createFeedback = '';

    public ?string $editFeedback = '';

    public ?string $deleteFeedback = '';

    public ?string $deleteContextName = null;

    protected $paginationTheme = 'tailwind';

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
        'filters.category_id' => ['except' => ''],
        'filters.units_id' => ['except' => ''],
    ];

    public function boot(ProductServices $productServices): void
    {
        $this->productServices = $productServices;
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

        return view('livewire.product.product-index', [
            'products' => $this->productServices->paginateProducts($this->search, $perPage, $this->filters),
            'categoryOptions' => Categories::query()->orderBy('name')->get(),
            'unitOptions' => Units::query()->orderBy('name')->get(),
            'perPageOptions' => $this->perPageOptions,
            'breadcumbs' => $this->breadcumb,
        ])->layout('components.layouts.app', [
            'title' => __('Product Management'),
        ]);
    }

    protected function createRules(): array
    {
        return $this->rulesFor('createForm');
    }

    protected function updateRules(int $productId): array
    {
        return $this->rulesFor('editForm', $productId);
    }

    protected function rulesFor(string $form, ?int $productId = null): array
    {
        $skuRule = Rule::unique('products', 'sku');

        if ($productId) {
            $skuRule->ignore($productId);
        }

        return [
            "{$form}.sku" => ['required', 'string', 'max:15', $skuRule],
            "{$form}.name" => ['required', 'string', 'max:150'],
            "{$form}.category_id" => ['required', Rule::exists('categories', 'id')],
            "{$form}.units_id" => ['required', Rule::exists('units', 'id')],
            "{$form}.track_stock" => ['required', 'boolean'],
            "{$form}.has_expiry" => ['required', 'boolean'],
            "{$form}.cost_price" => ['nullable', 'numeric', 'min:0'],
            "{$form}.sell_price" => ['nullable', 'numeric', 'min:0'],
            "{$form}.min_stock" => ['nullable', 'numeric', 'min:0'],
            "{$form}.is_active" => ['required', 'boolean'],
        ];
    }

    protected function formAttributes(string $form): array
    {
        return [
            "{$form}.sku" => __('SKU'),
            "{$form}.name" => __('Name'),
            "{$form}.category_id" => __('Category'),
            "{$form}.units_id" => __('Unit'),
            "{$form}.track_stock" => __('Track Stock'),
            "{$form}.has_expiry" => __('Has Expiry'),
            "{$form}.cost_price" => __('Cost Price'),
            "{$form}.sell_price" => __('Sell Price'),
            "{$form}.min_stock" => __('Minimum Stock'),
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
            'sku' => '',
            'name' => '',
            'category_id' => '',
            'units_id' => '',
            'track_stock' => 0,
            'has_expiry' => 0,
            'cost_price' => '',
            'sell_price' => '',
            'min_stock' => '',
            'is_active' => 1,
        ];
    }

    protected function resolvePerPage(int $value): int
    {
        return in_array($value, $this->perPageOptions, true)
            ? $value
            : $this->perPageOptions[0];
    }
}
