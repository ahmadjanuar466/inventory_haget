<?php

namespace App\Services\Inventory\Stock;

use App\Models\Stocks;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockImpl implements StockServices
{
    public function __construct(
        protected StockMovementServices $stockMovementServices
    ) {}

    public function addStock(array $data): Stocks
    {
        $qtyOnHand = $data['qty_on_hand'] ?? 0;

        return Stocks::firstOrCreate(
            [
                'warehouse_id' => $data['warehouse_id'],
                'product_id' => $data['product_id'],
            ],
            [
                'qty_on_hand' => $qtyOnHand,
                'qty_reserved' => $data['qty_reserved'] ?? 0,
                'qty_available' => $data['qty_available'] ?? $qtyOnHand,
                'last_movement_at' => $data['last_movement_at'] ?? null,
            ]
        );
    }

    public function increaseStock(Stocks $stock, array $data): Stocks
    {
        return DB::transaction(function () use ($stock, $data) {
            $stock = Stocks::query()->lockForUpdate()->findOrFail($stock->id);
            $movementData = $this->requiredMovementData($data, ['qty_in']);
            $qtyIn = (float) $data['qty_in'];

            $stock->qty_on_hand = (float) $stock->qty_on_hand + $qtyIn;
            $stock->qty_available = (float) $stock->qty_available + $qtyIn;
            $stock->last_movement_at = now();
            $stock->save();

            $this->stockMovementServices->recordStockMovement([
                'movement_date' => $data['movement_date'] ?? now(),
                'warehouse_id' => $stock->warehouse_id,
                'product_id' => $stock->product_id,
                'reference_type' => $movementData['reference_type'],
                'reference_id' => $movementData['reference_id'],
                'movement_type' => $data['movement_type'] ?? 'IN',
                'qty_in' => $qtyIn,
                'qty_out' => 0,
                'qty_balance_after' => $stock->qty_on_hand,
                'unit_cost' => $data['unit_cost'] ?? null,
                'notes' => $data['notes'] ?? null,
                'created_by' => $movementData['created_by'],
            ]);

            return $stock->refresh();
        });
    }

    public function decreaseStock(Stocks $stock, array $data): Stocks
    {
        return DB::transaction(function () use ($stock, $data) {
            $stock = Stocks::query()->lockForUpdate()->findOrFail($stock->id);
            $movementData = $this->requiredMovementData($data, ['qty_out']);
            $qtyOut = (float) $data['qty_out'];

            if ((float) $stock->qty_available < $qtyOut) {
                throw ValidationException::withMessages([
                    'qty_out' => 'Stok tersedia tidak mencukupi.',
                ]);
            }

            $stock->qty_on_hand = (float) $stock->qty_on_hand - $qtyOut;
            $stock->qty_available = (float) $stock->qty_available - $qtyOut;
            $stock->last_movement_at = now();
            $stock->save();

            $this->stockMovementServices->recordStockMovement([
                'movement_date' => $data['movement_date'] ?? now(),
                'warehouse_id' => $stock->warehouse_id,
                'product_id' => $stock->product_id,
                'reference_type' => $movementData['reference_type'],
                'reference_id' => $movementData['reference_id'],
                'movement_type' => $data['movement_type'] ?? 'OUT',
                'qty_in' => 0,
                'qty_out' => $qtyOut,
                'qty_balance_after' => $stock->qty_on_hand,
                'unit_cost' => $data['unit_cost'] ?? null,
                'notes' => $data['notes'] ?? null,
                'created_by' => $movementData['created_by'],
            ]);

            return $stock->refresh();
        });
    }

    public function getCurrentStock(int $warehouseId, int $productId): Stocks
    {
        return Stocks::where('warehouse_id', $warehouseId)
            ->where('product_id', $productId)
            ->firstOrFail();
    }

    public function paginateStock(string $search = '', int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        return Stocks::query()
            ->with(['product.units', 'warehouse'])
            ->when($search, function ($query) use ($search) {
                $query->whereHas('product', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%");
                });
            })
            ->when(! empty($filters['warehouse_id']), function ($query) use ($filters) {
                $query->where('warehouse_id', $filters['warehouse_id']);
            })
            ->when(! empty($filters['product_id']), function ($query) use ($filters) {
                $query->where('product_id', $filters['product_id']);
            })
            ->paginate($perPage);
    }

    protected function requiredMovementData(array $data, array $quantityFields): array
    {
        $rules = [
            'reference_type' => ['required', 'string'],
            'reference_id' => ['required', 'integer'],
            'created_by' => ['required', 'integer'],
        ];

        foreach ($quantityFields as $field) {
            $rules[$field] = ['required', 'numeric', 'min:0.01'];
        }

        return validator($data, $rules)->validate();
    }
}
