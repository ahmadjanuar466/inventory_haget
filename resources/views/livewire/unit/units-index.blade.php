<x-page-body>
    <x-breadcumbs :datas="$breadcumbs"></x-breadcumbs>

    @if ($deleteFeedback !== '')
        <x-delete-message>
            {{ $deleteFeedback }}
        </x-delete-message>
    @endif

    <x-page-title :title="$pageTitle['title']" :subtitle="$pageTitle['subtitle']"></x-page-title>

    <x-table-section>
        <x-table-navbar>
            <x-table-navbar-title title="{{ __('Units') }}"
                subtitle="{{ __('Maintain product measurement unit codes and names.') }}">
            </x-table-navbar-title>

            <x-table-navbar-search-button>
                <x-table-navbar-per-page-option modellive="perPage" :pageOptions="$perPageOptions">
                </x-table-navbar-per-page-option>

                <flux:input wire:model.live.debounce.400ms="search" :placeholder="__('Search units...')"
                    icon="magnifying-glass" class="sm:w-64" />

                <flux:button type="button" variant="primary" wire:click="openCreateModal">
                    {{ __('Add Unit') }}
                </flux:button>
            </x-table-navbar-search-button>
        </x-table-navbar>

        @php
            $dtHead = ['Code', 'Name', 'Products Used', 'Actions'];
        @endphp

        <x-table-custom>
            <x-table-head :head="$dtHead"></x-table-head>

            <x-table-body :items="$units" row-view="livewire.unit.partials.unit-row" :columns="4" item-key="unit"
                empty-message="{{ __('No units found.') }}" />
        </x-table-custom>

        <div class="pt-2">
            {{ $units->links() }}
        </div>
    </x-table-section>

    <x-modal name="create-unit-modal" class="max-w-2xl" title="{{ __('Add Unit') }}"
        subtitle="{{ __('Create a unit for product measurement.') }}" wire:model="showCreateModal"
        wire-target="insertUnits" :createFeedback="$createFeedback" close-action="closeCreateModal"
        loading-message="{{ __('Saving unit...') }}">
        <form wire:submit.prevent="insertUnits" class="space-y-4">
            <div class="grid gap-4 md:grid-cols-2">
                <flux:input wire:model.defer="createForm.code" :label="__('Code')" type="text" required autofocus />
                <flux:input wire:model.defer="createForm.name" :label="__('Name')" type="text" required />
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <flux:button type="button" variant="ghost" wire:click="closeCreateModal" wire:loading.attr="disabled"
                    wire:target="insertUnits">
                    {{ __('Cancel') }}
                </flux:button>

                <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="insertUnits">
                    <span wire:loading.remove wire:target="insertUnits">
                        {{ __('Create Unit') }}
                    </span>
                    <span wire:loading wire:target="insertUnits" class="inline-flex items-center gap-2">
                        <flux:icon.loading class="h-4 w-4" />
                        {{ __('Saving...') }}
                    </span>
                </flux:button>
            </div>
        </form>
    </x-modal>

    <x-modal name="edit-unit-modal" class="max-w-2xl" title="{{ __('Edit Unit') }}"
        subtitle="{{ __('Update product measurement unit identity.') }}" wire:model="showEditModal"
        wire-target="updateUnits" :createFeedback="$editFeedback" close-action="cancelEditing"
        loading-message="{{ __('Updating unit...') }}">
        <form wire:submit.prevent="updateUnits" class="space-y-4">
            <div class="grid gap-4 md:grid-cols-2">
                <flux:input wire:model.defer="editForm.code" :label="__('Code')" type="text" required />
                <flux:input wire:model.defer="editForm.name" :label="__('Name')" type="text" required />
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <flux:button type="button" variant="ghost" wire:click="cancelEditing" wire:loading.attr="disabled"
                    wire:target="updateUnits">
                    {{ __('Cancel') }}
                </flux:button>

                <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="updateUnits">
                    <span wire:loading.remove wire:target="updateUnits">
                        {{ __('Update Unit') }}
                    </span>
                    <span wire:loading wire:target="updateUnits" class="inline-flex items-center gap-2">
                        <flux:icon.loading class="h-4 w-4" />
                        {{ __('Saving...') }}
                    </span>
                </flux:button>
            </div>
        </form>
    </x-modal>

    <flux:modal name="delete-unit-modal" wire:model="showDeleteModal" focusable class="max-w-md"
        @close="$wire.cancelDelete()">
        <div class="relative space-y-6">
            <div wire:loading wire:target="deleteUnits"
                class="absolute inset-0 z-20 flex items-center justify-center rounded-xl bg-[#0d1a18]/70">
                <span class="inline-flex items-center gap-2 text-sm font-semibold text-[#d6c172]">
                    <flux:icon.loading class="h-5 w-5" />
                    {{ __('Deleting unit...') }}
                </span>
            </div>

            <div class="flex items-start justify-between gap-4">
                <div class="space-y-1">
                    <flux:heading size="lg">{{ __('Delete Unit') }}</flux:heading>
                    <flux:text variant="subtle">
                        {{ __('This action cannot be undone.') }}
                    </flux:text>
                </div>

                <flux:button variant="ghost" size="sm" icon="x-mark" wire:click="cancelDelete">
                    <span class="sr-only">{{ __('Close') }}</span>
                </flux:button>
            </div>

            <p class="text-sm text-[#a9c2bd]">
                {{ __('Are you sure you want to delete the unit ":name"?', ['name' => $deleteContextName]) }}
            </p>

            @error('delete')
                <p class="text-sm text-red-300">{{ $message }}</p>
            @enderror

            <div class="flex items-center justify-end gap-3">
                <flux:button type="button" variant="ghost" wire:click="cancelDelete" wire:loading.attr="disabled"
                    wire:target="deleteUnits">
                    {{ __('Cancel') }}
                </flux:button>

                <flux:button type="button" variant="danger" wire:click="deleteUnits" wire:loading.attr="disabled"
                    wire:target="deleteUnits">
                    <span wire:loading.remove wire:target="deleteUnits">
                        {{ __('Delete Unit') }}
                    </span>
                    <span wire:loading wire:target="deleteUnits" class="inline-flex items-center gap-2">
                        <flux:icon.loading class="h-4 w-4" />
                        {{ __('Deleting...') }}
                    </span>
                </flux:button>
            </div>
        </div>
    </flux:modal>
</x-page-body>
