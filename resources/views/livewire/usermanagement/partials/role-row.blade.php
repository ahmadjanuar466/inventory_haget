<tr class="transition hover:bg-[#0f2234]/60">
    <td class="px-4 py-3">
        <div class="font-semibold">{{ $role->name }}</div>
    </td>
    <td class="px-4 py-3">
        <span class="text-[#8fb3d9]">{{ $role->guard_name }}</span>
    </td>
    <td class="px-4 py-3">
        @if ($role->permissions->isEmpty())
            <span class="text-[#8fb3d9]">{{ __('No permissions assigned') }}</span>
        @else
            <div class="flex flex-wrap gap-2">
                @foreach ($role->permissions as $permission)
                    <span class="rounded-full border border-[#0f2234] bg-[#193549] px-2 py-1 text-xs text-[#ffc600]">
                        {{ $permission->name }}
                    </span>
                @endforeach
            </div>
        @endif
    </td>
    <td class="px-4 py-3">
        <div class="flex flex-wrap items-center justify-end gap-2">
            <flux:button type="button" size="sm" variant="ghost" wire:click="startEditing({{ $role->id }})"
                wire:loading.attr="disabled" wire:target="startEditing">
                {{ __('Edit') }}
            </flux:button>

            <flux:button type="button" size="sm" variant="danger" wire:click="confirmDelete({{ $role->id }})"
                wire:loading.attr="disabled" wire:target="confirmDelete">
                {{ __('Delete') }}
            </flux:button>
        </div>
    </td>
</tr>
