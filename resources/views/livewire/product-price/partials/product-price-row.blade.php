<tr class="transition hover:bg-[#142a28]/60">
    <td class="px-4 py-3">
        <div class="font-semibold">{{ $productPrice->product?->name ?? '-' }}</div>
        @if ($productPrice->product?->sku)
            <div class="text-sm text-[#a9c2bd]">{{ $productPrice->product->sku }}</div>
        @endif
    </td>
    <td class="px-4 py-3">
        <div class="font-semibold">{{ number_format((float) $productPrice->price, 2) }}</div>
    </td>
    <td class="px-4 py-3">
        @if ($productPrice->is_active)
            <span class="rounded-full bg-emerald-500/15 px-2.5 py-1 text-xs font-semibold text-emerald-200">
                {{ __('Active') }}
            </span>
        @else
            <span class="rounded-full bg-zinc-500/15 px-2.5 py-1 text-xs font-semibold text-zinc-300">
                {{ __('Inactive') }}
            </span>
        @endif
    </td>
    <td class="px-4 py-3">
        <div class="flex flex-wrap items-center justify-end gap-2">
            <flux:button type="button" size="sm" variant="ghost" wire:click="startEditing({{ $productPrice->id }})"
                wire:loading.attr="disabled" wire:target="startEditing">
                {{ __('Edit') }}
            </flux:button>

            <flux:button type="button" size="sm" variant="danger" wire:click="confirmDelete({{ $productPrice->id }})"
                wire:loading.attr="disabled" wire:target="confirmDelete">
                {{ __('Delete') }}
            </flux:button>
        </div>
    </td>
</tr>
