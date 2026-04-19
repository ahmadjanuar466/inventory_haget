@php
    $isCurrentUser = ($currentUserId ?? null) === $user->id;
@endphp

<tr class="transition hover:bg-[#142a28]/60">
    <td class="px-4 py-3">
        <div class="font-semibold">{{ $user->name }}</div>
    </td>
    <td class="px-4 py-3">
        <div class="text-[#a9c2bd]">{{ $user->email }}</div>
    </td>
    <td class="px-4 py-3">
        @if ($user->roles->isEmpty())
            <span class="text-[#a9c2bd]">{{ __('No roles') }}</span>
        @else
            <div class="flex flex-wrap gap-2">
                @foreach ($user->roles as $role)
                    <span class="rounded-full border border-[#142a28] bg-[#1c3432] px-2 py-1 text-xs text-[#d6c172]">
                        {{ $role->name }}
                    </span>
                @endforeach
            </div>
        @endif
    </td>
    <td class="px-4 py-3">
        @if ($user->permissions->isEmpty())
            <span class="text-[#a9c2bd]">{{ __('No permissions') }}</span>
        @else
            <div class="flex flex-wrap gap-2">
                @foreach ($user->permissions as $permission)
                    <span class="rounded-full border border-[#142a28] bg-[#243f3c] px-2 py-1 text-xs text-[#f4f1ec]">
                        {{ $permission->name }}
                    </span>
                @endforeach
            </div>
        @endif
    </td>
    <td class="px-4 py-3">
        <span class="text-[#a9c2bd]">{{ $user->created_at?->format('d M Y') }}</span>
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
                class="p-2 text-[#74c7b8] border-[#142a28] hover:bg-[#142a28]">
                <flux:icon.shield-check class="h-4 w-4" />
                <span class="sr-only">{{ __('Edit Access') }}</span>
            </flux:button>

            <flux:button type="button" size="sm" variant="outline" wire:click="startPasswordReset({{ $user->id }})"
                wire:loading.attr="disabled" wire:target="startPasswordReset"
                class="p-2 text-[#d6c172] border-[#142a28] hover:bg-[#142a28]">
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
