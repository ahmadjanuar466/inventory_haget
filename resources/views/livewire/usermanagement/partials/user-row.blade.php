@php
    $isCurrentUser = ($currentUserId ?? null) === $user->id;
@endphp

<tr class="transition hover:bg-[#0f2234]/60">
    <td class="px-4 py-3">
        <div class="font-semibold">{{ $user->name }}</div>
    </td>
    <td class="px-4 py-3">
        <div class="text-[#8fb3d9]">{{ $user->email }}</div>
    </td>
    <td class="px-4 py-3">
        @if ($user->roles->isEmpty())
            <span class="text-[#8fb3d9]">{{ __('No roles') }}</span>
        @else
            <div class="flex flex-wrap gap-2">
                @foreach ($user->roles as $role)
                    <span class="rounded-full border border-[#0f2234] bg-[#193549] px-2 py-1 text-xs text-[#ffc600]">
                        {{ $role->name }}
                    </span>
                @endforeach
            </div>
        @endif
    </td>
    <td class="px-4 py-3">
        @if ($user->permissions->isEmpty())
            <span class="text-[#8fb3d9]">{{ __('No permissions') }}</span>
        @else
            <div class="flex flex-wrap gap-2">
                @foreach ($user->permissions as $permission)
                    <span class="rounded-full border border-[#0f2234] bg-[#102a43] px-2 py-1 text-xs text-[#e6f1ff]">
                        {{ $permission->name }}
                    </span>
                @endforeach
            </div>
        @endif
    </td>
    <td class="px-4 py-3">
        <span class="text-[#8fb3d9]">{{ $user->created_at?->format('d M Y') }}</span>
    </td>
    <td class="px-4 py-3">
        <div class="flex flex-wrap items-center justify-end gap-2">
            <flux:button type="button" size="sm" variant="ghost" wire:click="startEditing({{ $user->id }})"
                wire:loading.attr="disabled" wire:target="startEditing" class="p-2">
                <flux:icon.pencil-square class="h-4 w-4" />
                <span class="sr-only">{{ __('Edit Profile') }}</span>
            </flux:button>

            <flux:button type="button" size="sm" variant="outline" wire:click="startAccessEditing({{ $user->id }})"
                wire:loading.attr="disabled" wire:target="startAccessEditing"
                class="p-2 text-[#61dafb] border-[#0f2234] hover:bg-[#0f2234]">
                <flux:icon.shield-check class="h-4 w-4" />
                <span class="sr-only">{{ __('Edit Access') }}</span>
            </flux:button>

            <flux:button type="button" size="sm" variant="outline" wire:click="startPasswordReset({{ $user->id }})"
                wire:loading.attr="disabled" wire:target="startPasswordReset"
                class="p-2 text-[#ffc600] border-[#0f2234] hover:bg-[#0f2234]">
                <flux:icon.key class="h-4 w-4" />
                <span class="sr-only">{{ __('Reset Password') }}</span>
            </flux:button>

            <flux:button type="button" size="sm" variant="danger" wire:click="confirmDelete({{ $user->id }})"
                wire:loading.attr="disabled" wire:target="confirmDelete" :disabled="$isCurrentUser"
                class="p-2 {{ $isCurrentUser ? 'cursor-not-allowed opacity-70' : '' }}">
                <flux:icon.trash class="h-4 w-4" />
                <span class="sr-only">{{ __('Delete User') }}</span>
            </flux:button>
        </div>
    </td>
</tr>
