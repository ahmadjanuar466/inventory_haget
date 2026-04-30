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
            <x-table-navbar-title title="{{ __('Suppliers') }}"
                subtitle="{{ __('Maintain supplier codes, contact details, and status.') }}">
            </x-table-navbar-title>

            <x-table-navbar-search-button>
                <x-table-navbar-per-page-option modellive="perPage" :pageOptions="$perPageOptions">
                </x-table-navbar-per-page-option>

                <flux:select wire:model.live="filters.is_active" class="sm:w-40">
                    <option value="">{{ __('All Status') }}</option>
                    <option value="1">{{ __('Active') }}</option>
                    <option value="0">{{ __('Inactive') }}</option>
                </flux:select>

                <flux:input wire:model.live.debounce.400ms="search" :placeholder="__('Search suppliers...')"
                    icon="magnifying-glass" class="sm:w-64" />

                <flux:button type="button" variant="primary" wire:click="openCreateModal">
                    {{ __('Add Supplier') }}
                </flux:button>
            </x-table-navbar-search-button>
        </x-table-navbar>

        @php
            $dtHead = ['Code', 'Supplier', 'Contact', 'Status', 'Actions'];
        @endphp

        <x-table-custom>
            <x-table-head :head="$dtHead"></x-table-head>

            <x-table-body :items="$suppliers" row-view="livewire.supplier.partials.supplier-row" :columns="5"
                item-key="supplier" empty-message="{{ __('No suppliers found.') }}" />
        </x-table-custom>

        <div class="pt-2">
            {{ $suppliers->links() }}
        </div>
    </x-table-section>

    <x-modal name="create-supplier-modal" class="max-w-2xl" title="{{ __('Add Supplier') }}"
        subtitle="{{ __('Create a supplier with contact details.') }}" wire:model="showCreateModal"
        wire-target="insertSupplier" :createFeedback="$createFeedback" close-action="closeCreateModal"
        loading-message="{{ __('Saving supplier...') }}">
        <form wire:submit.prevent="insertSupplier" class="space-y-4">
            <div class="grid gap-4 md:grid-cols-2">
                <flux:input wire:model.defer="createForm.code" :label="__('Code')" type="text" required autofocus />
                <flux:input wire:model.defer="createForm.name" :label="__('Name')" type="text" required />
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <flux:input wire:model.defer="createForm.contact_person" :label="__('Contact Person')" type="text" />
                <flux:input wire:model.defer="createForm.phone" :label="__('Phone')" type="text" />
            </div>

            <flux:input wire:model.defer="createForm.email" :label="__('Email')" type="email" />

            <flux:textarea wire:model.defer="createForm.address" :label="__('Address')" rows="3" />

            <flux:select wire:model.defer="createForm.is_active" :label="__('Status')" required>
                <option value="1">{{ __('Active') }}</option>
                <option value="0">{{ __('Inactive') }}</option>
            </flux:select>

            <div class="flex items-center justify-end gap-3 pt-2">
                <flux:button type="button" variant="ghost" wire:click="closeCreateModal" wire:loading.attr="disabled"
                    wire:target="insertSupplier">
                    {{ __('Cancel') }}
                </flux:button>

                <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="insertSupplier">
                    <span wire:loading.remove wire:target="insertSupplier">
                        {{ __('Create Supplier') }}
                    </span>
                    <span wire:loading wire:target="insertSupplier" class="inline-flex items-center gap-2">
                        <flux:icon.loading class="h-4 w-4" />
                        {{ __('Saving...') }}
                    </span>
                </flux:button>
            </div>
        </form>
    </x-modal>

    <x-modal name="edit-supplier-modal" class="max-w-2xl" title="{{ __('Edit Supplier') }}"
        subtitle="{{ __('Update supplier identity and contact details.') }}" wire:model="showEditModal"
        wire-target="updateSupplier" :createFeedback="$editFeedback" close-action="cancelEditing"
        loading-message="{{ __('Updating supplier...') }}">
        <form wire:submit.prevent="updateSupplier" class="space-y-4">
            <div class="grid gap-4 md:grid-cols-2">
                <flux:input wire:model.defer="editForm.code" :label="__('Code')" type="text" required />
                <flux:input wire:model.defer="editForm.name" :label="__('Name')" type="text" required />
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <flux:input wire:model.defer="editForm.contact_person" :label="__('Contact Person')" type="text" />
                <flux:input wire:model.defer="editForm.phone" :label="__('Phone')" type="text" />
            </div>

            <flux:input wire:model.defer="editForm.email" :label="__('Email')" type="email" />

            <flux:textarea wire:model.defer="editForm.address" :label="__('Address')" rows="3" />

            <flux:select wire:model.defer="editForm.is_active" :label="__('Status')" required>
                <option value="1">{{ __('Active') }}</option>
                <option value="0">{{ __('Inactive') }}</option>
            </flux:select>

            <div class="flex items-center justify-end gap-3 pt-2">
                <flux:button type="button" variant="ghost" wire:click="cancelEditing" wire:loading.attr="disabled"
                    wire:target="updateSupplier">
                    {{ __('Cancel') }}
                </flux:button>

                <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="updateSupplier">
                    <span wire:loading.remove wire:target="updateSupplier">
                        {{ __('Update Supplier') }}
                    </span>
                    <span wire:loading wire:target="updateSupplier" class="inline-flex items-center gap-2">
                        <flux:icon.loading class="h-4 w-4" />
                        {{ __('Saving...') }}
                    </span>
                </flux:button>
            </div>
        </form>
    </x-modal>

    <flux:modal name="delete-supplier-modal" wire:model="showDeleteModal" focusable class="max-w-md"
        @close="$wire.cancelDelete()">
        <div class="relative space-y-6">
            <div wire:loading wire:target="deleteSupplier"
                class="absolute inset-0 z-20 flex items-center justify-center rounded-xl bg-[#0d1a18]/70">
                <span class="inline-flex items-center gap-2 text-sm font-semibold text-[#d6c172]">
                    <flux:icon.loading class="h-5 w-5" />
                    {{ __('Deleting supplier...') }}
                </span>
            </div>

            <div class="flex items-start justify-between gap-4">
                <div class="space-y-1">
                    <flux:heading size="lg">{{ __('Delete Supplier') }}</flux:heading>
                    <flux:text variant="subtle">
                        {{ __('This action cannot be undone.') }}
                    </flux:text>
                </div>

                <flux:button variant="ghost" size="sm" icon="x-mark" wire:click="cancelDelete">
                    <span class="sr-only">{{ __('Close') }}</span>
                </flux:button>
            </div>

            <p class="text-sm text-[#a9c2bd]">
                {{ __('Are you sure you want to delete the supplier ":name"?', ['name' => $deleteContextName]) }}
            </p>

            @error('delete')
                <p class="text-sm text-red-300">{{ $message }}</p>
            @enderror

            <div class="flex items-center justify-end gap-3">
                <flux:button type="button" variant="ghost" wire:click="cancelDelete" wire:loading.attr="disabled"
                    wire:target="deleteSupplier">
                    {{ __('Cancel') }}
                </flux:button>

                <flux:button type="button" variant="danger" wire:click="deleteSupplier" wire:loading.attr="disabled"
                    wire:target="deleteSupplier">
                    <span wire:loading.remove wire:target="deleteSupplier">
                        {{ __('Delete Supplier') }}
                    </span>
                    <span wire:loading wire:target="deleteSupplier" class="inline-flex items-center gap-2">
                        <flux:icon.loading class="h-4 w-4" />
                        {{ __('Deleting...') }}
                    </span>
                </flux:button>
            </div>
        </div>
    </flux:modal>
</x-page-body>
