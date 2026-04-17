<tr class="transition hover:bg-[#0f2234]/60">
    <td class="px-4 py-3">
        <div class="font-semibold">{{ $branch->code }}</div>
    </td>
    <td class="px-4 py-3">
        <div class="font-semibold">{{ $branch->name }}</div>
        @if ($branch->address)
            <div class="max-w-xs truncate text-xs text-[#8fb3d9]">{{ $branch->address }}</div>
        @endif
    </td>
    <td class="px-4 py-3">
        <span class="text-[#8fb3d9]">{{ $branch->branchtype->name ?? __('Not assigned') }}</span>
    </td>
    <td class="px-4 py-3">
        <span class="text-[#8fb3d9]">{{ $branch->phone ?: __('Not provided') }}</span>
    </td>
    <td class="px-4 py-3">
        @if ((int) $branch->status === 1)
            <span class="rounded-full border border-green-500/30 bg-green-500/10 px-2 py-1 text-xs text-green-200">
                {{ __('Active') }}
            </span>
        @else
            <span class="rounded-full border border-red-500/30 bg-red-500/10 px-2 py-1 text-xs text-red-200">
                {{ __('Inactive') }}
            </span>
        @endif
    </td>
    <td class="px-4 py-3">
        <div class="flex flex-wrap items-center justify-end gap-2">
            <flux:button type="button" size="sm" variant="ghost" wire:click="startEditing({{ $branch->id }})"
                wire:loading.attr="disabled" wire:target="startEditing">
                {{ __('Edit') }}
            </flux:button>

            <flux:button type="button" size="sm" variant="danger" wire:click="confirmDelete({{ $branch->id }})"
                wire:loading.attr="disabled" wire:target="confirmDelete">
                {{ __('Delete') }}
            </flux:button>
        </div>
    </td>
</tr>
