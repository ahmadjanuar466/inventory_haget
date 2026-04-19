<tr class="transition hover:bg-[#142a28]/60">
    <td class="px-4 py-3">
        <div class="font-semibold">{{ $product->sku }}</div>
    </td>
    <td class="px-4 py-3">
        <div class="font-semibold">{{ $product->name }}</div>
        <div class="text-xs text-[#a9c2bd]">
            {{ __('Stock') }}: {{ $product->track_stock ? __('Tracked') : __('Not tracked') }}
            |
            {{ __('Expiry') }}: {{ $product->has_expiry ? __('Yes') : __('No') }}
        </div>
    </td>
    <td class="px-4 py-3">
        <span class="text-[#a9c2bd]">{{ $product->categories?->name ?? '-' }}</span>
    </td>
    <td class="px-4 py-3">
        <span class="text-[#a9c2bd]">{{ $product->units?->name ?? '-' }}</span>
    </td>
    <td class="px-4 py-3">
        <span class="text-[#a9c2bd]">{{ $product->cost_price ?? '-' }}</span>
    </td>
    <td class="px-4 py-3">
        <span class="text-[#a9c2bd]">{{ $product->sell_price ?? '-' }}</span>
    </td>
    <td class="px-4 py-3">
        <span @class([
            'inline-flex rounded px-2 py-1 text-xs font-semibold',
            'bg-emerald-500/15 text-emerald-200' => $product->is_active,
            'bg-red-500/15 text-red-200' => ! $product->is_active,
        ])>
            {{ $product->is_active ? __('Active') : __('Inactive') }}
        </span>
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
