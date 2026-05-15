<?php

namespace Tests\Unit;

use App\Models\Categories;
use App\Models\Products;
use App\Models\Units;
use App\Services\Products\ProductUnitImpl;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductUnitImplTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_saves_one_stock_unit_conversion_from_purchase_unit(): void
    {
        [$product, $baseUnit, $additionalUnit] = $this->makeProductFixture();

        $service = new ProductUnitImpl();

        $service->syncProductUnits($product, $baseUnit->id, [
            [
                'unit_id' => $additionalUnit->id,
                'conversion_qty' => '12,5',
                'is_active' => '1',
            ],
        ]);

        $product->refresh()->load('productUnits');

        $this->assertCount(1, $product->productUnits);

        $stockUnitRow = $product->productUnits->first();

        $this->assertSame($additionalUnit->id, $stockUnitRow->unit_id);
        $this->assertSame('12.50', (string) $stockUnitRow->conversion_qty);
        $this->assertSame(1, $stockUnitRow->is_active);
        $this->assertSame(0, $stockUnitRow->is_base);
    }

    public function test_it_defaults_blank_conversion_qty_to_one_for_stock_unit(): void
    {
        [$product, $baseUnit, $additionalUnit] = $this->makeProductFixture();

        $service = new ProductUnitImpl();

        $service->syncProductUnits($product, $baseUnit->id, [
            [
                'unit_id' => $additionalUnit->id,
                'conversion_qty' => '',
                'is_active' => '1',
            ],
        ]);

        $product->refresh()->load('productUnits');

        $stockUnitRow = $product->productUnits->firstWhere('unit_id', $additionalUnit->id);

        $this->assertNotNull($stockUnitRow);
        $this->assertSame('1.00', (string) $stockUnitRow->conversion_qty);
        $this->assertSame(0, $stockUnitRow->is_base);
    }

    protected function makeProductFixture(): array
    {
        $category = Categories::create([
            'code' => 'CAT-001',
            'name' => 'Beverages',
        ]);

        $baseUnit = Units::create([
            'code' => 'PCS',
            'name' => 'Pieces',
        ]);

        $additionalUnit = Units::create([
            'code' => 'BOX',
            'name' => 'Box',
        ]);

        $product = Products::create([
            'sku' => 'SKU-001',
            'name' => 'Kurma Milk',
            'category_id' => $category->id,
            'units_id' => $baseUnit->id,
            'track_stock' => 1,
            'has_expiry' => 0,
            'cost_price' => 10000,
            'sell_price' => 12000,
            'min_stock' => 5,
            'is_active' => 1,
        ]);

        return [$product, $baseUnit, $additionalUnit];
    }
}
