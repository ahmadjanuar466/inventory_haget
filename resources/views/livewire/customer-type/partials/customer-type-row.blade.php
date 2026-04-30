<tr class="transition hover:bg-[#142a28]/60">
    <td class="px-4 py-3">
        <div class="font-semibold">{{ $customerType->name }}</div>
    </td>
    <td class="px-4 py-3">
        <span class="text-[#a9c2bd]">{{ $customerType->customers_count ?? $customerType->customers->count() }}</span>
    </td>
    <td class="px-4 py-3">
        <div class="flex flex-wrap items-center justify-end gap-2">
            <flux:button type="button" size="sm" variant="ghost" wire:click="startEditing({{ $customerType->id }})"
                wire:loading.attr="disabled" wire:target="startEditing">
                {{ __('Edit') }}
            </flux:button>

            <flux:button type="button" size="sm" variant="danger" wire:click="confirmDelete({{ $customerType->id }})"
                wire:loading.attr="disabled" wire:target="confirmDelete">
                {{ __('Delete') }}
            </flux:button>
        </div>
    </td>
</tr>
