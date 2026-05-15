@php
    $stockUnit = $stock->product?->productUnits
        ?->first(fn ($productUnit) => (int) $productUnit->is_active === 1)
        ?->unit;
    $unitName = $stockUnit->name ?? $stock->product->units->name ?? __('Unit');
    $lastMovementAt = $stock->last_movement_at?->format('d M Y H:i') ?? '-';
    $availableQty = (float) $stock->qty_available;
    $onHandQty = (float) $stock->qty_on_hand;
    $isOutOfStock = $availableQty <= 0;
    $isLowStock = !$isOutOfStock && $onHandQty > 0 && $availableQty <= $onHandQty * 0.2;
@endphp

<tr class="transition hover:bg-[#142a28]/50">
    <td class="px-4 py-3">
        <div class="max-w-[280px]">
            <div class="truncate font-semibold text-[#f4f1ec]">{{ $stock->product->name ?? __('Unknown Product') }}</div>
            <div
                class="mt-1 inline-flex rounded-full border border-[#142a28]/80 bg-[#10211f]/70 px-2 py-0.5 text-xs text-[#a9c2bd]">
                {{ $stock->product->sku ?? '-' }}
            </div>
        </div>
    </td>
    <td class="px-4 py-3">
        <div class="max-w-[220px]">
            <div class="truncate font-medium text-[#f4f1ec]">{{ $stock->warehouse->name ?? __('Unknown Warehouse') }}
            </div>
            <div class="text-xs text-[#a9c2bd]">{{ $stock->warehouse->code ?? '-' }}</div>
        </div>
    </td>
    <td class="px-4 py-3 text-right tabular-nums">
        <div class="font-semibold text-[#f4f1ec]">{{ number_format($onHandQty, 2) }}</div>
        <div class="text-xs text-[#a9c2bd]">{{ $unitName }}</div>
    </td>
    <td class="px-4 py-3 text-right tabular-nums">
        <div class="font-semibold text-[#f4f1ec]">{{ number_format((float) $stock->qty_reserved, 2) }}</div>
        <div class="text-xs text-[#a9c2bd]">{{ $unitName }}</div>
    </td>
    <td class="px-4 py-3 text-right tabular-nums">
        <div
            class="font-semibold {{ $isOutOfStock ? 'text-red-300' : ($isLowStock ? 'text-amber-200' : 'text-[#f4e8a4]') }}">
            {{ number_format($availableQty, 2) }}
        </div>
        <div class="mt-1 flex justify-end">
            <span
                class="rounded-full border px-2 py-0.5 text-xs {{ $isOutOfStock ? 'border-red-400/40 bg-red-500/10 text-red-200' : ($isLowStock ? 'border-amber-300/40 bg-amber-400/10 text-amber-100' : 'border-[#d6c172]/30 bg-[#d6c172]/10 text-[#f4e8a4]') }}">
                {{ $isOutOfStock ? __('Empty') : ($isLowStock ? __('Low') : $unitName) }}
            </span>
        </div>
    </td>
    <td class="px-4 py-3 whitespace-nowrap">
        <div class="text-sm text-[#f4f1ec]">{{ $lastMovementAt }}</div>
        <div class="text-xs text-[#a9c2bd]">{{ $stock->updated_at?->diffForHumans() ?? '-' }}</div>
    </td>
    <td class="px-4 py-3">
        <div class="flex flex-wrap items-center justify-end gap-2">
            <flux:button type="button" size="sm" variant="ghost" icon="clipboard-document-list"
                wire:click="showStockCard({{ $stock->product_id }}, {{ $stock->warehouse_id }})"
                wire:loading.attr="disabled" wire:target="showStockCard">
                {{ __('Card') }}
            </flux:button>
        </div>
    </td>
</tr>
