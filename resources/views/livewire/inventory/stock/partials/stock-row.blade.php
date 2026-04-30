@php
    $unitName = $stock->product->units->name ?? __('Unit');
    $lastMovementAt = $stock->last_movement_at?->format('d M Y H:i') ?? '-';
@endphp

<tr class="transition hover:bg-[#142a28]/60">
    <td class="px-4 py-3">
        <div class="font-semibold">{{ $stock->product->name ?? __('Unknown Product') }}</div>
        <div class="text-xs text-[#a9c2bd]">{{ $stock->product->sku ?? '-' }}</div>
    </td>
    <td class="px-4 py-3">
        <div class="font-semibold">{{ $stock->warehouse->name ?? __('Unknown Warehouse') }}</div>
        <div class="text-xs text-[#a9c2bd]">{{ $stock->warehouse->code ?? '-' }}</div>
    </td>
    <td class="px-4 py-3 text-right">
        <div class="font-semibold">{{ number_format((float) $stock->qty_on_hand, 2) }}</div>
        <div class="text-xs text-[#a9c2bd]">{{ $unitName }}</div>
    </td>
    <td class="px-4 py-3 text-right">
        <div class="font-semibold">{{ number_format((float) $stock->qty_reserved, 2) }}</div>
        <div class="text-xs text-[#a9c2bd]">{{ $unitName }}</div>
    </td>
    <td class="px-4 py-3 text-right">
        <div class="font-semibold text-[#f4e8a4]">{{ number_format((float) $stock->qty_available, 2) }}</div>
        <div class="text-xs text-[#a9c2bd]">{{ $unitName }}</div>
    </td>
    <td class="px-4 py-3 text-[#a9c2bd] whitespace-nowrap">
        {{ $lastMovementAt }}
    </td>
    <td class="px-4 py-3">
        <div class="flex flex-wrap items-center justify-end gap-2">
            <flux:button type="button" size="sm" variant="ghost" icon="clipboard-document-list"
                wire:click="showStockCard({{ $stock->product_id }}, {{ $stock->warehouse_id }})"
                wire:loading.attr="disabled" wire:target="showStockCard">
                {{ __('Stock Card') }}
            </flux:button>
        </div>
    </td>
</tr>
