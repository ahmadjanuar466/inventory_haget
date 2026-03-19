<tr class="transition hover:bg-[#0f2234]/60">
    <td class="px-4 py-3">
        <div class="font-semibold">{{ $permission->name }}</div>
    </td>
    <td class="px-4 py-3">
        <span class="text-[#8fb3d9]">{{ $permission->guard_name }}</span>
    </td>
    <td class="px-4 py-3">
        <span class="rounded-full border border-[#0f2234] bg-[#193549] px-2 py-1 text-xs text-[#ffc600]">
            {{ $permission->roles_count }}
            {{ \Illuminate\Support\Str::plural('role', $permission->roles_count) }}
        </span>
    </td>
    <td class="px-4 py-3">
        <div class="flex flex-wrap items-center justify-end gap-2">
            <flux:button type="button" size="sm" variant="ghost" wire:click="startEditing({{ $permission->id }})"
                wire:loading.attr="disabled" wire:target="startEditing">
                {{ __('Edit') }}
            </flux:button>

            <flux:button type="button" size="sm" variant="danger" wire:click="confirmDelete({{ $permission->id }})"
                wire:loading.attr="disabled" wire:target="confirmDelete">
                {{ __('Delete') }}
            </flux:button>
        </div>
    </td>
</tr>
