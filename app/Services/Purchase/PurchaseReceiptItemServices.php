<?php

namespace App\Services\Purchase;

use App\Models\PurchaseReceiptItems;
use Illuminate\Pagination\LengthAwarePaginator;

interface PurchaseReceiptItemServices
{
    public function createPurchaseReceiptItem(array $data):PurchaseReceiptItems;
    public function updatePurchaseReceiptItem(PurchaseReceiptItems $purchaseReceiptItems, array $data):PurchaseReceiptItems;
    public function deletePurchaseReceiptItem(PurchaseReceiptItems $purchaseReceiptItems):bool;
    public function deletePurchaseReceiptItemsByPurchaseReceiptId(int $purchaseReceiptId):bool;
    public function getPurchaseReceiptItemById(int $id):PurchaseReceiptItems;
    public function getPurchaseReceiptItemsByPurchaseReceiptId(int $purchaseReceiptId):array;
    public function paginatePurchaseReceiptItems(String $search = '', int $perPage = 10, array $filters = []): LengthAwarePaginator;
    
}
