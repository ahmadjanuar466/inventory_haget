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

    public function test_it_saves_manual_conversion_qty_for_additional_units(): void
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

        $this->assertCount(2, $product->productUnits);

        $baseRow = $product->productUnits->firstWhere('is_base', 1);
        $additionalRow = $product->productUnits->firstWhere('unit_id', $additionalUnit->id);

        $this->assertNotNull($baseRow);
        $this->assertSame($baseUnit->id, $baseRow->unit_id);
        $this->assertSame('1.00', (string) $baseRow->conversion_qty);

        $this->assertNotNull($additionalRow);
        $this->assertSame($additionalUnit->id, $additionalRow->unit_id);
        $this->assertSame('12.50', (string) $additionalRow->conversion_qty);
        $this->assertSame(1, $additionalRow->is_active);
        $this->assertSame(0, $additionalRow->is_base);
    }

    public function test_it_defaults_blank_conversion_qty_to_one(): void
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

        $additionalRow = $product->productUnits->firstWhere('unit_id', $additionalUnit->id);

        $this->assertNotNull($additionalRow);
        $this->assertSame('1.00', (string) $additionalRow->conversion_qty);
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
