@php
    $minStock = $product->min_stock !== null ? number_format((float) $product->min_stock, 2) : '-';
    $additionalUnits = $product->productUnits->where('is_base', 0);
@endphp

<tr class="transition hover:bg-[#142a28]/60">
    <td class="px-4 py-3">
        <div class="font-semibold">{{ $product->sku }}</div>
    </td>
    <td class="px-4 py-3">
        <div class="font-semibold">{{ $product->name }}</div>
        <div class="text-xs text-[#a9c2bd]">{{ __('Minimum Stock: :value', ['value' => $minStock]) }}</div>
    </td>
    <td class="px-4 py-3">
        <span class="text-[#a9c2bd]">{{ $product->categories->name ?? __('No category') }}</span>
    </td>
    <td class="px-4 py-3">
        <span class="text-[#a9c2bd]">{{ $product->units->name ?? __('No unit') }}</span>

        @if ($additionalUnits->isNotEmpty())
            <div class="mt-2 space-y-1 text-xs text-[#a9c2bd]">
                @foreach ($additionalUnits as $productUnit)
                    <div>
                        {{ __('1 :base = :qty :unit', ['base' => $product->units->name ?? __('Base Unit'), 'qty' => $productUnit->conversion_qty, 'unit' => $productUnit->unit->name ?? __('Unit')]) }}
                    </div>
                @endforeach
            </div>
        @endif
    </td>
    <td class="px-4 py-3">
        @if ((int) $product->is_active === 1)
            <span class="rounded-full border border-green-500/30 bg-green-500/10 px-2 py-1 text-xs text-green-200">
                {{ __('Active') }}
            </span>
        @else
            <span class="rounded-full border border-red-500/30 bg-red-500/10 px-2 py-1 text-xs text-red-200">
                {{ __('Inactive') }}
            </span>
        @endif

        <div class="mt-2 space-y-1 text-xs text-[#a9c2bd]">
            <div>
                {{ (int) $product->track_stock === 1 ? __('Track stock enabled') : __('Track stock disabled') }}
            </div>
            <div>
                {{ (int) $product->has_expiry === 1 ? __('Expiry tracking enabled') : __('No expiry tracking') }}
            </div>
        </div>
    </td>
    <td class="px-4 py-3">
        <div class="flex flex-wrap items-center justify-end gap-2">
            <flux:button type="button" size="sm" variant="ghost" wire:click="startEditing({{ $product->id }})"
                wire:loading.attr="disabled" wire:target="startEditing">
                {{ __('Edit') }}
            </flux:button>

            <flux:button type="button" size="sm" variant="danger" wire:click="confirmDelete({{ $product->id }})"
                wire:loading.attr="disabled" wire:target="confirmDelete">
                {{ __('Delete') }}
            </flux:button>
        </div>
    </td>
</tr>
