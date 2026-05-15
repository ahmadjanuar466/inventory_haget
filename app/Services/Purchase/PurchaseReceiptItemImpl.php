<?php

namespace App\Services\Purchase;

use App\Models\PurchaseReceiptItems;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Override;

class PurchaseReceiptItemImpl implements PurchaseReceiptItemServices
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    public function createPurchaseReceiptItem(array $data): PurchaseReceiptItems
    {
        return PurchaseReceiptItems::create($this->preparePayload($data));
    }
    #[Override]
    public function updatePurchaseReceiptItem(PurchaseReceiptItems $purchaseReceiptItems, array $data): PurchaseReceiptItems
    {
        $purchaseReceiptItems->update($this->preparePayload($data));
        return $purchaseReceiptItems->refresh();
    }
    #[Override]
    public function deletePurchaseReceiptItem(PurchaseReceiptItems $purchaseReceiptItems): bool
    {
        return (bool) $purchaseReceiptItems->delete();
    }
    #[Override]
    public function deletePurchaseReceiptItemsByPurchaseReceiptId(int $purchaseReceiptId): bool
    {
        return (bool) PurchaseReceiptItems::where('purchase_receipt_id', $purchaseReceiptId)->delete();
    }
    #[Override]
    public function getPurchaseReceiptItemById(int $id): PurchaseReceiptItems
    {
        return PurchaseReceiptItems::with(['purchaseReceipt', 'product', 'unit'])->findOrFail($id);
    }
    public function getPurchaseReceiptItemsByPurchaseReceiptId(int $purchaseReceiptId): array
    {
        return PurchaseReceiptItems::query()->with(['purchaseReceipt', 'product', 'unit'])->where('purchase_receipt_id', $purchaseReceiptId)->get()->toArray();
    }
    #[Override]
    public function paginatePurchaseReceiptItems(string $search = '', int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        $query = PurchaseReceiptItems::query()->with(['purchaseReceipt', 'product', 'unit'])
        ->when($search, function ($q) use ($search) {
            $q->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'like', "%$search%");
            })->orWhereHas('purchaseReceipt', function ($q) use ($search) {
                $q->where('receipt_no', 'like', "%$search%");
            })->orWhereHas('unit', function ($q) use ($search) {
                $q->where('name', 'like', "%$search%");
            });
        })
        ->when(isset($filters['product_id']) && $filters['product_id'] !== '', function ($q) use ($filters) {
            $q->where('product_id', $filters['product_id']);
        });

        return $query->paginate($perPage);
    }

    protected function preparePayload(array $data): array
    {
        if (isset($data['quantity']) && ! isset($data['qty'])) {
            $data['qty'] = $data['quantity'];
        }

        if (isset($data['unit_cost']) && ! isset($data['cost_price'])) {
            $data['cost_price'] = $data['unit_cost'];
        }

        return Arr::only($data, [
            'purchase_receipt_id',
            'product_id',
            'qty',
            'unit_id',
            'conversion_qty',
            'cost_price',
            'discount_amount',
            'tax_amount',
            'subtotal',
        ]);
    }
}
