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

class ProductsIndex extends Component
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
        'subtitle' => 'Manage product identity, unit, stock tracking, and active status.',
    ];

    public string $search = '';

    public int $perPage = 10;

    public array $perPageOptions = [10, 20, 30, 40, 50];

    public array $filters = [
        'category_id' => '',
        'units_id' => '',
        'is_active' => '',
    ];

    public array $createForm = [
        'sku' => '',
        'name' => '',
        'category_id' => '',
        'units_id' => '',
        'product_units' => [],
        'track_stock' => '0',
        'has_expiry' => '0',
        'min_stock' => '',
        'is_active' => '1',
    ];

    public array $editForm = [
        'sku' => '',
        'name' => '',
        'category_id' => '',
        'units_id' => '',
        'product_units' => [],
        'track_stock' => '0',
        'has_expiry' => '0',
        'min_stock' => '',
        'is_active' => '1',
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
        'filters.is_active' => ['except' => ''],
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

    public function updatedCreateFormUnitsId($value): void
    {
        $this->syncProductUnitRowsForForm('createForm', $value);
    }

    public function updatedEditFormUnitsId($value): void
    {
        $this->syncProductUnitRowsForForm('editForm', $value);
    }

    public function render(): View
    {
        $perPage = $this->resolvePerPage((int) $this->perPage);

        if ($perPage !== $this->perPage) {
            $this->perPage = $perPage;
        }

        return view('livewire.product.products-index', [
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
            "{$form}.product_units" => ['array'],
            "{$form}.product_units.*.unit_id" => [
                'required',
                'distinct',
                Rule::exists('units', 'id'),
                Rule::notIn(array_filter([
                    data_get($this, "{$form}.units_id"),
                ])),
            ],
            "{$form}.product_units.*.conversion_qty" => ['nullable', 'numeric', 'gt:0'],
            "{$form}.product_units.*.is_active" => ['required', Rule::in(['0', '1', 0, 1])],
            "{$form}.track_stock" => ['required', Rule::in(['0', '1', 0, 1])],
            "{$form}.has_expiry" => ['required', Rule::in(['0', '1', 0, 1])],
            "{$form}.min_stock" => ['nullable', 'numeric', 'min:0'],
            "{$form}.is_active" => ['required', Rule::in(['0', '1', 0, 1])],
        ];
    }

    protected function formAttributes(string $form): array
    {
        return [
            "{$form}.sku" => __('SKU'),
            "{$form}.name" => __('Name'),
            "{$form}.category_id" => __('Category'),
            "{$form}.units_id" => __('Base Unit'),
            "{$form}.product_units.*.unit_id" => __('Additional Unit'),
            "{$form}.product_units.*.conversion_qty" => __('Qty for Selected Unit'),
            "{$form}.product_units.*.is_active" => __('Unit Status'),
            "{$form}.track_stock" => __('Track Stock'),
            "{$form}.has_expiry" => __('Has Expiry'),
            "{$form}.min_stock" => __('Minimum Stock'),
            "{$form}.is_active" => __('Status'),
        ];
    }

    protected function formErrorKeys(string $form): array
    {
        $keys = array_keys($this->formAttributes($form));

        foreach (($this->{$form}['product_units'] ?? []) as $index => $item) {
            $keys[] = "{$form}.product_units.{$index}.unit_id";
            $keys[] = "{$form}.product_units.{$index}.conversion_qty";
            $keys[] = "{$form}.product_units.{$index}.is_active";
        }

        return $keys;
    }

    protected function defaultForm(): array
    {
        return [
            'sku' => '',
            'name' => '',
            'category_id' => '',
            'units_id' => '',
            'product_units' => [],
            'track_stock' => '0',
            'has_expiry' => '0',
            'min_stock' => '',
            'is_active' => '1',
        ];
    }

    protected function normalizeProductPayload(array $payload): array
    {
        foreach (['min_stock'] as $field) {
            if (($payload[$field] ?? '') === '') {
                $payload[$field] = null;
            }
        }

        $payload['cost_price'] = null;
        $payload['sell_price'] = null;

        foreach (['category_id', 'units_id', 'track_stock', 'has_expiry', 'is_active'] as $field) {
            if (($payload[$field] ?? '') !== '') {
                $payload[$field] = (int) $payload[$field];
            }
        }

        $payload['product_units'] = collect($payload['product_units'] ?? [])
            ->map(function ($item) {
                return [
                    'unit_id' => ($item['unit_id'] ?? '') === '' ? null : (int) $item['unit_id'],
                    'conversion_qty' => $this->nullableNormalizedDecimal($item['conversion_qty'] ?? ''),
                    'is_active' => ($item['is_active'] ?? '') === '' ? 1 : (int) $item['is_active'],
                ];
            })
            ->values()
            ->all();

        $payload['sku'] = trim((string) ($payload['sku'] ?? ''));
        $payload['name'] = trim((string) ($payload['name'] ?? ''));

        return $payload;
    }

    protected function prepareProductFormForValidation(string $form): void
    {
        if (! in_array($form, ['createForm', 'editForm'], true)) {
            return;
        }

        $state = $this->{$form};
        $state['product_units'] = $this->sanitizeProductUnitRows($state['product_units'] ?? []);
        $this->{$form} = $state;
    }

    protected function sanitizeProductUnitRows(array $rows): array
    {
        return collect($rows)
            ->map(function ($row) {
                return [
                    'unit_id' => trim((string) ($row['unit_id'] ?? '')),
                    'conversion_qty' => $this->normalizeDecimalInput($row['conversion_qty'] ?? ''),
                    'is_active' => (string) ($row['is_active'] ?? '1'),
                ];
            })
            ->filter(function (array $row) {
                return ! ($row['unit_id'] === '' && $row['conversion_qty'] === '');
            })
            ->values()
            ->all();
    }

    protected function defaultProductUnitRow(): array
    {
        return [
            'unit_id' => '',
            'conversion_qty' => '',
            'is_active' => '1',
        ];
    }

    public function addProductUnitRow(string $form): void
    {
        if (! in_array($form, ['createForm', 'editForm'], true)) {
            return;
        }

        $this->{$form}['product_units'][] = $this->defaultProductUnitRow();
        $this->resetErrorBag($this->formErrorKeys($form));
    }

    public function removeProductUnitRow(string $form, int $index): void
    {
        if (! in_array($form, ['createForm', 'editForm'], true)) {
            return;
        }

        if (! isset($this->{$form}['product_units'][$index])) {
            return;
        }

        unset($this->{$form}['product_units'][$index]);
        $this->{$form}['product_units'] = array_values($this->{$form}['product_units']);
        $this->resetErrorBag($this->formErrorKeys($form));
    }

    protected function resolvePerPage(int $value): int
    {
        return in_array($value, $this->perPageOptions, true)
            ? $value
            : $this->perPageOptions[0];
    }

    protected function syncProductUnitRowsForForm(string $form, mixed $baseUnitId): void
    {
        if (! in_array($form, ['createForm', 'editForm'], true)) {
            return;
        }

        if ((string) $baseUnitId === '') {
            $this->{$form}['product_units'] = [];

            return;
        }

        if (empty($this->{$form}['product_units'])) {
            $this->{$form}['product_units'] = [$this->defaultProductUnitRow()];
        }
    }

    protected function normalizeDecimalInput(mixed $value): string
    {
        $normalized = trim((string) $value);

        if ($normalized === '') {
            return '';
        }

        return str_replace(',', '.', $normalized);
    }

    protected function nullableNormalizedDecimal(mixed $value): ?string
    {
        $normalized = $this->normalizeDecimalInput($value);

        return $normalized === '' ? null : $normalized;
    }
}
