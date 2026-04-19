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
            <x-table-navbar-title title="{{ __('Permissions') }}"
                subtitle="{{ __('Keep your permission catalogue tidy and up to date.') }}">
            </x-table-navbar-title>

            <x-table-navbar-search-button>
                <x-table-navbar-per-page-option modellive="perPage" :pageOptions="$perPageOptions">
                </x-table-navbar-per-page-option>

                <flux:input wire:model.live.debounce.400ms="search" :placeholder="__('Search permissions...')"
                    icon="magnifying-glass" class="sm:w-64" />

                <flux:button type="button" variant="primary" wire:click="openCreateModal">
                    {{ __('Add Permission') }}
                </flux:button>
            </x-table-navbar-search-button>
        </x-table-navbar>

        @php
            $dtHead = ['Name', 'Guard', 'Roles Using', 'Actions'];
        @endphp

        <x-table-custom>
            <x-table-head :head="$dtHead"></x-table-head>

            <x-table-body :items="$permissions" row-view="livewire.usermanagement.partials.permission-row" :columns="4"
                item-key="permission" empty-message="{{ __('No permissions found.') }}" />
        </x-table-custom>

        <div class="pt-2">
            {{ $permissions->links() }}
        </div>
    </x-table-section>

    <x-modal name="create-permission-modal" class="max-w-lg" title="{{ __('Add Permission') }}"
        subtitle="{{ __('Create a new permission that can later be assigned to roles.') }}" wire:model="showCreateModal"
        wire-target="createPermission" :createFeedback="$createFeedback" close-action="closeCreateModal"
        loading-message="{{ __('Saving permission...') }}">
        <form wire:submit.prevent="createPermission" class="space-y-4">
            <flux:input wire:model.defer="createForm.name" :label="__('Name')" type="text" required autofocus />

            <flux:input wire:model.defer="createForm.guard_name" :label="__('Guard Name')" type="text" required />

            <div class="flex items-center justify-end gap-3 pt-2">
                <flux:button type="button" variant="ghost" wire:click="closeCreateModal" wire:loading.attr="disabled"
                    wire:target="createPermission">
                    {{ __('Cancel') }}
                </flux:button>

                <flux:button type="submit" variant="primary" wire:loading.attr="disabled"
                    wire:target="createPermission">
                    <span wire:loading.remove wire:target="createPermission">
                        {{ __('Create Permission') }}
                    </span>
                    <span wire:loading wire:target="createPermission" class="inline-flex items-center gap-2">
                        <flux:icon.loading class="h-4 w-4" />
                        {{ __('Saving...') }}
                    </span>
                </flux:button>
            </div>
        </form>
    </x-modal>

    <x-modal name="edit-permission-modal" class="max-w-lg" title="{{ __('Edit Permission') }}"
        subtitle="{{ __('Rename the permission or change the guard it belongs to.') }}" wire:model="showEditModal"
        wire-target="updatePermission" :createFeedback="$editFeedback" close-action="cancelEditing"
        loading-message="{{ __('Updating permission...') }}">
        <form wire:submit.prevent="updatePermission" class="space-y-4">
            <flux:input wire:model.defer="editForm.name" :label="__('Name')" type="text" required />

            <flux:input wire:model.defer="editForm.guard_name" :label="__('Guard Name')" type="text" required />

            <div class="flex items-center justify-end gap-3 pt-2">
                <flux:button type="button" variant="ghost" wire:click="cancelEditing" wire:loading.attr="disabled"
                    wire:target="updatePermission">
                    {{ __('Cancel') }}
                </flux:button>

                <flux:button type="submit" variant="primary" wire:loading.attr="disabled"
                    wire:target="updatePermission">
                    <span wire:loading.remove wire:target="updatePermission">
                        {{ __('Update Permission') }}
                    </span>
                    <span wire:loading wire:target="updatePermission" class="inline-flex items-center gap-2">
                        <flux:icon.loading class="h-4 w-4" />
                        {{ __('Saving...') }}
                    </span>
                </flux:button>
            </div>
        </form>
    </x-modal>

    <flux:modal name="delete-permission-modal" wire:model="showDeleteModal" focusable class="max-w-md"
        @close="$wire.cancelDelete()">
        <div class="relative space-y-6">
            <div wire:loading wire:target="deletePermission"
                class="absolute inset-0 z-20 flex items-center justify-center rounded-xl bg-[#0d1a18]/70">
                <span class="inline-flex items-center gap-2 text-sm font-semibold text-[#d6c172]">
                    <flux:icon.loading class="h-5 w-5" />
                    {{ __('Deleting permission...') }}
                </span>
            </div>

            <div class="flex items-start justify-between gap-4">
                <div class="space-y-1">
                    <flux:heading size="lg">{{ __('Delete Permission') }}</flux:heading>
                    <flux:text variant="subtle">
                        {{ __('This action cannot be undone.') }}
                    </flux:text>
                </div>

                <flux:button variant="ghost" size="sm" icon="x-mark" wire:click="cancelDelete">
                    <span class="sr-only">{{ __('Close') }}</span>
                </flux:button>
            </div>

            <p class="text-sm text-[#a9c2bd]">
                {{ __('Are you sure you want to delete the permission ":name"?', ['name' => $deleteContextName]) }}
            </p>

            <div class="flex items-center justify-end gap-3">
                <flux:button type="button" variant="ghost" wire:click="cancelDelete" wire:loading.attr="disabled"
                    wire:target="deletePermission">
                    {{ __('Cancel') }}
                </flux:button>

                <flux:button type="button" variant="danger" wire:click="deletePermission"
                    wire:loading.attr="disabled" wire:target="deletePermission">
                    <span wire:loading.remove wire:target="deletePermission">
                        {{ __('Delete Permission') }}
                    </span>
                    <span wire:loading wire:target="deletePermission" class="inline-flex items-center gap-2">
                        <flux:icon.loading class="h-4 w-4" />
                        {{ __('Deleting...') }}
                    </span>
                </flux:button>
            </div>
        </div>
    </flux:modal>
</x-page-body>
