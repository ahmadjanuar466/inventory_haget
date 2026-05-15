<?php

namespace App\Services\Purchase;

use App\Models\Products;
use App\Models\ProductUnits;
use App\Models\PurchaseReceiptItems;
use App\Models\PurchaseReceipts;
use App\Models\Suppliers;
use App\Models\Units;
use App\Models\Warehouse;
use App\Services\Inventory\Stock\StockServices;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PurchaseReceiptImpl implements PurchaseReceiptServices
{
    public function __construct(
        private StockServices $stockServices,
        private PurchaseReceiptItemServices $purchaseReceiptItemServices
    ) {}

    public function createPurchaseReceipt(array $purchaseData, array $purchaseItemData): PurchaseReceipts
    {
        return DB::transaction(function () use ($purchaseData, $purchaseItemData) {
            $purchaseReceipt = PurchaseReceipts::create($this->preparePayload($purchaseData));

            foreach ($this->normalizeItemPayloads($purchaseItemData) as $itemPayload) {
                $purchaseReceiptItem = $this->purchaseReceiptItemServices->createPurchaseReceiptItem(
                    array_merge($itemPayload, ['purchase_receipt_id' => $purchaseReceipt->id])
                );

                $this->increaseStockFromPurchaseItem($purchaseReceipt, $purchaseReceiptItem);
            }

            return $purchaseReceipt->refresh()->load(['supplier', 'warehouse', 'items.product', 'items.unit']);
        });
    }

    public function updatePurchaseReceipt(PurchaseReceipts $purchaseReceipts, array $purchaseData, array $purchaseItemData): PurchaseReceipts
    {
        return DB::transaction(function () use ($purchaseReceipts, $purchaseData, $purchaseItemData) {
            $oldWarehouseId = (int) $purchaseReceipts->warehouse_id;
            $oldItem = $purchaseReceipts->items()->first();
            $oldItemData = $oldItem ? $this->snapshotItem($oldItem) : null;

            $purchaseReceipts->update($this->preparePayload($purchaseData));

            $itemPayload = array_merge(
                $this->normalizeItemPayload($purchaseItemData),
                ['purchase_receipt_id' => $purchaseReceipts->id]
            );

            if ($oldItem) {
                $purchaseReceiptItem = $this->purchaseReceiptItemServices->updatePurchaseReceiptItem($oldItem, $itemPayload);
                $this->syncStockAfterItemUpdate($purchaseReceipts->refresh(), $purchaseReceiptItem, $oldItemData, $oldWarehouseId);
            } else {
                $purchaseReceiptItem = $this->purchaseReceiptItemServices->createPurchaseReceiptItem($itemPayload);
                $this->increaseStockFromPurchaseItem($purchaseReceipts->refresh(), $purchaseReceiptItem);
            }

            return $purchaseReceipts->refresh()->load(['supplier', 'warehouse', 'items.product', 'items.unit']);
        });
    }

    public function deletePurchaseReceipt(PurchaseReceipts $purchaseReceipts): bool
    {
        return DB::transaction(function () use ($purchaseReceipts) {
            $purchaseReceipts->loadMissing('items');

            foreach ($purchaseReceipts->items as $item) {
                $this->decreaseStockFromPurchaseItem($purchaseReceipts, $item);
            }

            $this->purchaseReceiptItemServices->deletePurchaseReceiptItemsByPurchaseReceiptId($purchaseReceipts->id);

            return (bool) $purchaseReceipts->delete();
        });
    }

    public function getPurchaseReceiptById(int $id): PurchaseReceipts
    {
        return PurchaseReceipts::query()
            ->with(['supplier', 'warehouse', 'items.product', 'items.unit'])
            ->findOrFail($id);
    }

    public function getPurchaseReceiptsBySupplierId(int $supplierId): array
    {
        return PurchaseReceipts::query()
            ->with(['supplier', 'warehouse', 'items.product', 'items.unit'])
            ->where('supplier_id', $supplierId)
            ->get()
            ->toArray();
    }

    public function paginatePurchaseReceipts(string $search = '', int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        $query = PurchaseReceipts::query()
            ->with(['supplier', 'warehouse'])
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($subQuery) use ($search) {
                    $subQuery->where('receipt_no', 'like', "%{$search}%")
                        ->orWhere('invoice_no', 'like', "%{$search}%");
                });
            })
            ->when(isset($filters['supplier_id']) && $filters['supplier_id'] !== '', function ($q) use ($filters) {
                $q->where('supplier_id', $filters['supplier_id']);
            })
            ->when(isset($filters['warehouse_id']) && $filters['warehouse_id'] !== '', function ($q) use ($filters) {
                $q->where('warehouse_id', $filters['warehouse_id']);
            })
            ->when(isset($filters['status']) && $filters['status'] !== '', function ($q) use ($filters) {
                $q->where('status', $filters['status']);
            });

        return $query->paginate($perPage);
    }

    public function getSelectedItemsReceipt(?int $purchaseReceiptId): ?PurchaseReceipts
    {
        if (! $purchaseReceiptId) {
            return null;
        }

        return PurchaseReceipts::query()
            ->with(['supplier', 'warehouse', 'items.product.productUnits.unit', 'items.unit'])
            ->find($purchaseReceiptId);
    }

    public function getSupplierOptions(): Collection
    {
        return Suppliers::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function getWarehouseOptions(): Collection
    {
        return Warehouse::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function getProductOptions(): Collection
    {
        return Products::query()
            ->with(['units', 'productUnits.unit'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function getUnitOptions(): Collection
    {
        return Units::query()
            ->orderBy('name')
            ->get();
    }

    public function getProductById(int $productId): ?Products
    {
        return Products::query()->find($productId);
    }

    public function getActiveProductUnit(int $productId): ?ProductUnits
    {
        return ProductUnits::query()
            ->where('product_id', $productId)
            ->where('is_active', 1)
            ->first();
    }

    protected function preparePayload(array $data): array
    {
        return Arr::only($data, [
            'receipt_no',
            'receipt_date',
            'supplier_id',
            'warehouse_id',
            'invoice_no',
            'invoice_file',
            'status',
            'subtotal',
            'discount_amount',
            'tax_amount',
            'total_amount',
            'notes',
            'created_by',
            'approved_by',
        ]);
    }

    protected function normalizeItemPayload(array $data): array
    {
        if (isset($data['quantity']) && ! isset($data['qty'])) {
            $data['qty'] = $data['quantity'];
        }

        if (isset($data['unit_cost']) && ! isset($data['cost_price'])) {
            $data['cost_price'] = $data['unit_cost'];
        }

        return $data;
    }

    protected function normalizeItemPayloads(array $data): array
    {
        if (array_is_list($data)) {
            return array_map(fn (array $item): array => $this->normalizeItemPayload($item), $data);
        }

        return [$this->normalizeItemPayload($data)];
    }

    protected function syncStockAfterItemUpdate(
        PurchaseReceipts $purchaseReceipt,
        PurchaseReceiptItems $newItem,
        ?array $oldItemData,
        int $oldWarehouseId
    ): void {
        if ($oldItemData === null) {
            $this->increaseStockFromPurchaseItem($purchaseReceipt, $newItem);
            return;
        }

        $newWarehouseId = (int) $purchaseReceipt->warehouse_id;
        $oldProductId = (int) $oldItemData['product_id'];
        $newProductId = (int) $newItem->product_id;

        if ($oldWarehouseId !== $newWarehouseId || $oldProductId !== $newProductId) {
            $this->decreaseStock(
                $purchaseReceipt,
                $oldWarehouseId,
                $oldProductId,
                $this->stockQuantityFromArray($oldItemData),
                $oldItemData['cost_price']
            );
            $this->increaseStockFromPurchaseItem($purchaseReceipt, $newItem);
            return;
        }

        $quantityDifference = $this->stockQuantityFromItem($newItem) - $this->stockQuantityFromArray($oldItemData);

        if ($quantityDifference > 0) {
            $this->increaseStockFromPurchaseItem($purchaseReceipt, $newItem, $quantityDifference);
        }

        if ($quantityDifference < 0) {
            $this->decreaseStockFromPurchaseItem($purchaseReceipt, $newItem, abs($quantityDifference));
        }
    }

    protected function increaseStockFromPurchaseItem(PurchaseReceipts $purchaseReceipt, PurchaseReceiptItems $item, ?float $quantity = null): void
    {
        $stock = $this->stockServices->addStock([
            'warehouse_id' => $purchaseReceipt->warehouse_id,
            'product_id' => $item->product_id,
        ]);

        $this->stockServices->increaseStock($stock, [
            'reference_type' => PurchaseReceipts::class,
            'reference_id' => $purchaseReceipt->id,
            'qty_in' => $quantity ?? $this->stockQuantityFromItem($item),
            'movement_type' => 'PURCHASE_RECEIPT_IN',
            'unit_cost' => $item->cost_price,
            'created_by' => $purchaseReceipt->created_by,
        ]);
    }

    protected function decreaseStockFromPurchaseItem(PurchaseReceipts $purchaseReceipt, PurchaseReceiptItems $item, ?float $quantity = null): void
    {
        $this->decreaseStock(
            $purchaseReceipt,
            (int) $purchaseReceipt->warehouse_id,
            (int) $item->product_id,
            $quantity ?? $this->stockQuantityFromItem($item),
            $item->cost_price
        );
    }

    protected function decreaseStock(
        PurchaseReceipts $purchaseReceipt,
        int $warehouseId,
        int $productId,
        float $quantity,
        mixed $costPrice
    ): void {
        $stock = $this->stockServices->getCurrentStock($warehouseId, $productId);

        $this->stockServices->decreaseStock($stock, [
            'reference_type' => PurchaseReceipts::class,
            'reference_id' => $purchaseReceipt->id,
            'qty_out' => $quantity,
            'movement_type' => 'PURCHASE_RECEIPT_OUT',
            'unit_cost' => $costPrice,
            'created_by' => $purchaseReceipt->created_by,
        ]);
    }

    protected function stockQuantityFromItem(PurchaseReceiptItems $item): float
    {
        return $this->stockQuantityFromArray($this->snapshotItem($item));
    }

    protected function stockQuantityFromArray(array $item): float
    {
        $conversionQty = (float) ($item['conversion_qty'] ?? 0);

        if ($conversionQty > 0) {
            return $conversionQty;
        }

        return (float) ($item['qty'] ?? 0);
    }

    protected function snapshotItem(PurchaseReceiptItems $item): array
    {
        return [
            'product_id' => $item->product_id,
            'qty' => $item->qty,
            'conversion_qty' => $item->conversion_qty,
            'cost_price' => $item->cost_price,
        ];
    }
}
