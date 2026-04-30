<?php

namespace App\Livewire\Inventory\Stock;

use App\Models\StockMovements;
use App\Models\Warehouse;
use App\Services\Inventory\Stock\StockServices;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\WithPagination;

class StockIndex extends Component
{
    use WithPagination;

    protected StockServices $stockServices;

    public array $breadcumb = [
        ['title' => 'Dashboard', 'routes' => 'dashboard'],
        ['title' => 'Inventory', 'routes' => ''],
        ['title' => 'Stock', 'routes' => ''],
        ['title' => 'List Stock', 'routes' => ''],
    ];

    public array $pageTitle = [
        'title' => 'Stock Management',
        'subtitle' => 'Monitor product stock balances across warehouses.',
    ];

    public string $search = '';

    public int $perPage = 10;

    public array $perPageOptions = [10, 20, 30, 40, 50];

    public array $filters = [
        'warehouse_id' => '',
        'product_id' => '',
    ];

    public bool $showStockCardModal = false;

    public ?int $selectedProductId = null;

    public ?int $selectedWarehouseId = null;

    protected $paginationTheme = 'tailwind';

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
        'filters.warehouse_id' => ['except' => ''],
        'filters.product_id' => ['except' => ''],
    ];

    public function boot(StockServices $stockServices): void
    {
        $this->stockServices = $stockServices;
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

    public function showStockCard(int $productId, int $warehouseId): void
    {
        $this->selectedProductId = $productId;
        $this->selectedWarehouseId = $warehouseId;
        $this->showStockCardModal = true;
    }

    public function closeStockCardModal(): void
    {
        $this->showStockCardModal = false;
        $this->selectedProductId = null;
        $this->selectedWarehouseId = null;
    }

    public function render(): View
    {
        $perPage = $this->resolvePerPage((int) $this->perPage);

        if ($perPage !== $this->perPage) {
            $this->perPage = $perPage;
        }

        $selectedStock = null;
        $stockMovements = collect();

        if ($this->selectedProductId && $this->selectedWarehouseId) {
            $selectedStock = $this->stockServices->getCurrentStock(
                $this->selectedWarehouseId,
                $this->selectedProductId,
            )->load(['product.units', 'warehouse']);

            $stockMovements = $this->stockMovements();
        }

        return view('livewire.inventory.stock.stock-index', [
            'stocks' => $this->stockServices->paginateStock($this->search, $perPage, $this->filters),
            'warehouseOptions' => Warehouse::query()->orderBy('name')->get(),
            'stockMovements' => $stockMovements,
            'selectedStock' => $selectedStock,
            'perPageOptions' => $this->perPageOptions,
            'breadcumbs' => $this->breadcumb,
        ])->layout('components.layouts.app', [
            'title' => __('Stock Management'),
        ]);
    }

    protected function stockMovements(): Collection
    {
        return StockMovements::query()
            ->with(['product', 'warehouse', 'creator'])
            ->where('product_id', $this->selectedProductId)
            ->where('warehouse_id', $this->selectedWarehouseId)
            ->orderByDesc('movement_date')
            ->limit(25)
            ->get();
    }

    protected function resolvePerPage(int $value): int
    {
        return in_array($value, $this->perPageOptions, true)
            ? $value
            : $this->perPageOptions[0];
    }
}
