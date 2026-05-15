<?php

namespace App\Services\Purchase;

use App\Models\Products;
use App\Models\ProductUnits;
use App\Models\PurchaseReceipts;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface PurchaseReceiptServices
{
    //
    public function createPurchaseReceipt(array $purchaseData,array $purchaseItemData):PurchaseReceipts;
    public function updatePurchaseReceipt(PurchaseReceipts $purchaseReceipts, array $purchaseData,array $purchaseItemData):PurchaseReceipts;
    public function deletePurchaseReceipt(PurchaseReceipts $purchaseReceipts):bool;
    public function getPurchaseReceiptById(int $id):PurchaseReceipts;
    public function getPurchaseReceiptsBySupplierId(int $supplierId):array;
    public function paginatePurchaseReceipts(string $search = '', int $perPage = 10, array $filters = []): LengthAwarePaginator;
    public function getSelectedItemsReceipt(?int $purchaseReceiptId): ?PurchaseReceipts;
    public function getSupplierOptions(): Collection;
    public function getWarehouseOptions(): Collection;
    public function getProductOptions(): Collection;
    public function getUnitOptions(): Collection;
    public function getProductById(int $productId): ?Products;
    public function getActiveProductUnit(int $productId): ?ProductUnits;
}
