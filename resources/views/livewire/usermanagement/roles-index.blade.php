<x-page-body>

    <x-breadcumbs :datas="$breadcumb"></x-breadcumbs>
    @if ($deleteFeedback !== '')
        <x-delete-message>
            {{ $deleteFeedback }}
        </x-delete-message>
    @endif

    <x-page-title :title="$pageTitle['title']" :subtitle="$pageTitle['subtitle']"></x-page-title>

    <x-table-section>
        <x-table-navbar>
            <x-table-navbar-title title="{{ __('Roles') }}"
                subtitle="{{ __('Organize your permissions by assigning them to reusable roles.') }}">
            </x-table-navbar-title>

            <x-table-navbar-search-button>
                <x-table-navbar-per-page-option modellive="perPage" :pageOptions="$perPageOptions">
                </x-table-navbar-per-page-option>

                <flux:input wire:model.live.debounce.400ms="search" :placeholder="__('Search roles...')"
                    icon="magnifying-glass" class="sm:w-64" />

                <flux:button type="button" variant="primary" wire:click="openCreateModal">
                    {{ __('Add Role') }}
                </flux:button>
            </x-table-navbar-search-button>
        </x-table-navbar>

        @php
            $dtHead = ['Name', 'Guard', 'Permissions', 'Actions'];
        @endphp

        <x-table-custom>
            <x-table-head :head="$dtHead"></x-table-head>

            <x-table-body :items="$roles" row-view="livewire.usermanagement.partials.role-row" :columns="4"
                item-key="role" empty-message="{{ __('No roles found.') }}" />
        </x-table-custom>

        <div class="pt-2">
            {{ $roles->links() }}
        </div>
    </x-table-section>

    <x-modal name="create-role-modal" class="max-w-2xl" title="{{ __('Add Role') }}"
        subtitle="{{ __('Define a new role and assign permissions to it.') }}" wire:model="showCreateModal"
        wire-target="createRole" :createFeedback="$createFeedback" close-action="closeCreateModal"
        loading-message="{{ __('Saving role...') }}">
        <form wire:submit.prevent="createRole" class="space-y-4">
            <flux:input wire:model.defer="createForm.name" :label="__('Name')" type="text" required autofocus />

            <flux:input wire:model.defer="createForm.guard_name" :label="__('Guard Name')" type="text" required />

            <div>
                <label class="mb-2 block text-sm font-medium text-[#e6f1ff]">
                    {{ __('Permissions') }}
                </label>

                <select wire:model.defer="createForm.permissions" multiple size="6"
                    class="w-full rounded-lg border border-[#0f2234]/70 bg-[#0b1624]/70 px-3 py-2 text-sm text-[#e6f1ff] focus:border-[#ffc600] focus:outline-none focus:ring-0">
                    @foreach ($availablePermissions as $permission)
                        <option value="{{ $permission->name }}">{{ $permission->name }}</option>
                    @endforeach
                </select>

                <p class="mt-2 text-xs text-[#8fb3d9]">
                    {{ __('Hold Ctrl (Windows) or Command (Mac) to select multiple permissions.') }}
                </p>

                @error('createForm.permissions')
                    <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                @enderror
                @error('createForm.permissions.*')
                    <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <flux:button type="button" variant="ghost" wire:click="closeCreateModal" wire:loading.attr="disabled"
                    wire:target="createRole">
                    {{ __('Cancel') }}
                </flux:button>

                <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="createRole">
                    <span wire:loading.remove wire:target="createRole">
                        {{ __('Create Role') }}
                    </span>
                    <span wire:loading wire:target="createRole" class="inline-flex items-center gap-2">
                        <flux:icon.loading class="h-4 w-4" />
                        {{ __('Saving...') }}
                    </span>
                </flux:button>
            </div>
        </form>
    </x-modal>

    <x-modal name="edit-role-modal" class="max-w-2xl" title="{{ __('Edit Role') }}"
        subtitle="{{ __('Update the role details or adjust the assigned permissions.') }}" wire:model="showEditModal"
        wire-target="updateRole" :createFeedback="$editFeedback" close-action="cancelEditing"
        loading-message="{{ __('Updating role...') }}">
        <form wire:submit.prevent="updateRole" class="space-y-4">
            <flux:input wire:model.defer="editForm.name" :label="__('Name')" type="text" required />

            <flux:input wire:model.defer="editForm.guard_name" :label="__('Guard Name')" type="text" required />

            <div>
                <label class="mb-2 block text-sm font-medium text-[#e6f1ff]">
                    {{ __('Permissions') }}
                </label>

                <select wire:model.defer="editForm.permissions" multiple size="6"
                    class="w-full rounded-lg border border-[#0f2234]/70 bg-[#0b1624]/70 px-3 py-2 text-sm text-[#e6f1ff] focus:border-[#ffc600] focus:outline-none focus:ring-0">
                    @foreach ($availablePermissions as $permission)
                        <option value="{{ $permission->name }}">{{ $permission->name }}</option>
                    @endforeach
                </select>

                @error('editForm.permissions')
                    <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                @enderror
                @error('editForm.permissions.*')
                    <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <flux:button type="button" variant="ghost" wire:click="cancelEditing" wire:loading.attr="disabled"
                    wire:target="updateRole">
                    {{ __('Cancel') }}
                </flux:button>

                <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="updateRole">
                    <span wire:loading.remove wire:target="updateRole">
                        {{ __('Update Role') }}
                    </span>
                    <span wire:loading wire:target="updateRole" class="inline-flex items-center gap-2">
                        <flux:icon.loading class="h-4 w-4" />
                        {{ __('Saving...') }}
                    </span>
                </flux:button>
            </div>
        </form>
    </x-modal>

    <flux:modal name="delete-role-modal" wire:model="showDeleteModal" focusable class="max-w-md"
        @close="$wire.cancelDelete()">
        <div class="relative space-y-6">
            <div wire:loading wire:target="deleteRole"
                class="absolute inset-0 z-20 flex items-center justify-center rounded-xl bg-[#0b1424]/70">
                <span class="inline-flex items-center gap-2 text-sm font-semibold text-[#ffc600]">
                    <flux:icon.loading class="h-5 w-5" />
                    {{ __('Deleting role...') }}
                </span>
            </div>

            <div class="flex items-start justify-between gap-4">
                <div class="space-y-1">
                    <flux:heading size="lg">{{ __('Delete Role') }}</flux:heading>
                    <flux:text variant="subtle">
                        {{ __('This action cannot be undone.') }}
                    </flux:text>
                </div>

                <flux:button variant="ghost" size="sm" icon="x-mark" wire:click="cancelDelete">
                    <span class="sr-only">{{ __('Close') }}</span>
                </flux:button>
            </div>

            <p class="text-sm text-[#8fb3d9]">
                {{ __('Are you sure you want to delete the role ":name"?', ['name' => $deleteContextName]) }}
            </p>

            <div class="flex items-center justify-end gap-3">
                <flux:button type="button" variant="ghost" wire:click="cancelDelete" wire:loading.attr="disabled"
                    wire:target="deleteRole">
                    {{ __('Cancel') }}
                </flux:button>

                <flux:button type="button" variant="danger" wire:click="deleteRole" wire:loading.attr="disabled"
                    wire:target="deleteRole">
                    <span wire:loading.remove wire:target="deleteRole">
                        {{ __('Delete Role') }}
                    </span>
                    <span wire:loading wire:target="deleteRole" class="inline-flex items-center gap-2">
                        <flux:icon.loading class="h-4 w-4" />
                        {{ __('Deleting...') }}
                    </span>
                </flux:button>
            </div>
        </div>
    </flux:modal>
</x-page-body>
