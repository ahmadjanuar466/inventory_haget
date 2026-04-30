<?php

namespace App\Services\Purchase;

use App\Models\PurchaseReceipts;
use App\Services\Inventory\Stock\StockServices;
use App\Services\Supplier\SupplierServices;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Override;

class PurchaseReceiptImpl implements PurchaseReceiptServices
{
    private StockServices $stockServices;
    /**
     * Create a new class instance.
     */
    public function __construct(StockServices $stockServices)
    {
        //
        $this->stockServices = $stockServices;
    }
    public function createPurchaseReceipt(array $data): PurchaseReceipts
    {
        return DB::transaction(function () use ($data) {
           // $this->stockServices->increaseStock()
            // $supplier = $this->supplierService->getSupplierById($data['supplier_id']);
            // if (!$supplier) {
            //     throw new \Exception('Supplier not found');
            // }
            return PurchaseReceipts::create($this->preparePayload($data));
        }); 
    }
    public function updatePurchaseReceipt(PurchaseReceipts $purchaseReceipts, array $data): PurchaseReceipts
    {
        $purchaseReceipts->update($this->preparePayload($data));
        return $purchaseReceipts->refresh();
    }
    public function deletePurchaseReceipt(PurchaseReceipts $purchaseReceipts): bool
    {
        if($purchaseReceipts->items()->exists()){
            return false;
        }
        return (bool) $purchaseReceipts->delete();
    }
    public function getPurchaseReceiptById(int $id): PurchaseReceipts
    {
        return PurchaseReceipts::query()->with(['supplier', 'warehouse'])->findOrFail($id);
    }
    public function getPurchaseReceiptsBySupplierId(int $supplierId): array
    {
        return PurchaseReceipts::query()
        ->with(['supplier', 'warehouse'])->where('supplier_id', $supplierId)
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

    protected function preparePayload(array $data): array
    {
        return Arr::only($data, [
            'receipt_no',
            'receipt_date',
            'supplier_id',
            'warehouse_id',
            'invoice_no',
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
}
