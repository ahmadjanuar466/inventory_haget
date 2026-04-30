<?php

namespace App\Services\Purchase;

use App\Models\PurchaseReceipts;
use Illuminate\Pagination\LengthAwarePaginator;

interface PurchaseReceiptServices
{
    //
    public function createPurchaseReceipt(array $purchaseData,array $purchaseItemData):PurchaseReceipts;
    public function updatePurchaseReceipt(PurchaseReceipts $purchaseReceipts, array $data):PurchaseReceipts;
    public function deletePurchaseReceipt(PurchaseReceipts $purchaseReceipts):bool;
    public function getPurchaseReceiptById(int $id):PurchaseReceipts;
    public function getPurchaseReceiptsBySupplierId(int $supplierId):array;
    public function paginatePurchaseReceipts(string $search = '', int $perPage = 10, array $filters = []): LengthAwarePaginator;
}
