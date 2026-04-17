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
            <x-table-navbar-title title="{{ __('Warehouses') }}"
                subtitle="{{ __('Maintain warehouse identities, assigned branches, and active status.') }}">
            </x-table-navbar-title>

            <x-table-navbar-search-button>
                <x-table-navbar-per-page-option modellive="perPage" :pageOptions="$perPageOptions">
                </x-table-navbar-per-page-option>

                <flux:select wire:model.live="filters.branch_id" class="sm:w-52">
                    <option value="">{{ __('All Branches') }}</option>
                    @foreach ($branchOptions as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </flux:select>

                <flux:select wire:model.live="filters.is_active" class="sm:w-40">
                    <option value="">{{ __('All Status') }}</option>
                    <option value="1">{{ __('Active') }}</option>
                    <option value="0">{{ __('Inactive') }}</option>
                </flux:select>

                <flux:input wire:model.live.debounce.400ms="search" :placeholder="__('Search warehouses...')"
                    icon="magnifying-glass" class="sm:w-64" />

                <flux:button type="button" variant="primary" wire:click="openCreateModal">
                    {{ __('Add Warehouse') }}
                </flux:button>
            </x-table-navbar-search-button>
        </x-table-navbar>

        @php
            $dtHead = ['Code', 'Name', 'Branch', 'Status', 'Actions'];
        @endphp

        <x-table-custom>
            <x-table-head :head="$dtHead"></x-table-head>

            <x-table-body :items="$warehouses" row-view="livewire.warehouse.partials.warehouse-row" :columns="5"
                item-key="warehouse" empty-message="{{ __('No warehouses found.') }}" />
        </x-table-custom>

        <div class="pt-2">
            {{ $warehouses->links() }}
        </div>
    </x-table-section>

    <x-modal name="create-warehouse-modal" class="max-w-2xl" title="{{ __('Add Warehouse') }}"
        subtitle="{{ __('Create a warehouse with its branch, code, name, and status.') }}"
        wire:model="showCreateModal" wire-target="createWarehouse" :createFeedback="$createFeedback"
        close-action="closeCreateModal" loading-message="{{ __('Saving warehouse...') }}">
        <form wire:submit.prevent="createWarehouse" class="space-y-4">
            <flux:select wire:model.defer="createForm.branch_id" :label="__('Branch')" required autofocus>
                <option value="">{{ __('Select branch') }}</option>
                @foreach ($branchOptions as $branch)
                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                @endforeach
            </flux:select>

            <div class="grid gap-4 md:grid-cols-2">
                <flux:input wire:model.defer="createForm.code" :label="__('Code')" type="text" required />
                <flux:input wire:model.defer="createForm.name" :label="__('Name')" type="text" required />
            </div>

            <flux:select wire:model.defer="createForm.is_active" :label="__('Status')" required>
                <option value="1">{{ __('Active') }}</option>
                <option value="0">{{ __('Inactive') }}</option>
            </flux:select>

            <div class="flex items-center justify-end gap-3 pt-2">
                <flux:button type="button" variant="ghost" wire:click="closeCreateModal" wire:loading.attr="disabled"
                    wire:target="createWarehouse">
                    {{ __('Cancel') }}
                </flux:button>

                <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="createWarehouse">
                    <span wire:loading.remove wire:target="createWarehouse">
                        {{ __('Create Warehouse') }}
                    </span>
                    <span wire:loading wire:target="createWarehouse" class="inline-flex items-center gap-2">
                        <flux:icon.loading class="h-4 w-4" />
                        {{ __('Saving...') }}
                    </span>
                </flux:button>
            </div>
        </form>
    </x-modal>

    <x-modal name="edit-warehouse-modal" class="max-w-2xl" title="{{ __('Edit Warehouse') }}"
        subtitle="{{ __('Update warehouse branch, identity, and active status.') }}" wire:model="showEditModal"
        wire-target="updateWarehouse" :createFeedback="$editFeedback" close-action="cancelEditing"
        loading-message="{{ __('Updating warehouse...') }}">
        <form wire:submit.prevent="updateWarehouse" class="space-y-4">
            <flux:select wire:model.defer="editForm.branch_id" :label="__('Branch')" required>
                <option value="">{{ __('Select branch') }}</option>
                @foreach ($branchOptions as $branch)
                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                @endforeach
            </flux:select>

            <div class="grid gap-4 md:grid-cols-2">
                <flux:input wire:model.defer="editForm.code" :label="__('Code')" type="text" required />
                <flux:input wire:model.defer="editForm.name" :label="__('Name')" type="text" required />
            </div>

            <flux:select wire:model.defer="editForm.is_active" :label="__('Status')" required>
                <option value="1">{{ __('Active') }}</option>
                <option value="0">{{ __('Inactive') }}</option>
            </flux:select>

            <div class="flex items-center justify-end gap-3 pt-2">
                <flux:button type="button" variant="ghost" wire:click="cancelEditing" wire:loading.attr="disabled"
                    wire:target="updateWarehouse">
                    {{ __('Cancel') }}
                </flux:button>

                <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="updateWarehouse">
                    <span wire:loading.remove wire:target="updateWarehouse">
                        {{ __('Update Warehouse') }}
                    </span>
                    <span wire:loading wire:target="updateWarehouse" class="inline-flex items-center gap-2">
                        <flux:icon.loading class="h-4 w-4" />
                        {{ __('Saving...') }}
                    </span>
                </flux:button>
            </div>
        </form>
    </x-modal>

    <flux:modal name="delete-warehouse-modal" wire:model="showDeleteModal" focusable class="max-w-md"
        @close="$wire.cancelDelete()">
        <div class="relative space-y-6">
            <div wire:loading wire:target="deleteWarehouse"
                class="absolute inset-0 z-20 flex items-center justify-center rounded-xl bg-[#0b1424]/70">
                <span class="inline-flex items-center gap-2 text-sm font-semibold text-[#ffc600]">
                    <flux:icon.loading class="h-5 w-5" />
                    {{ __('Deleting warehouse...') }}
                </span>
            </div>

            <div class="flex items-start justify-between gap-4">
                <div class="space-y-1">
                    <flux:heading size="lg">{{ __('Delete Warehouse') }}</flux:heading>
                    <flux:text variant="subtle">
                        {{ __('This action cannot be undone.') }}
                    </flux:text>
                </div>

                <flux:button variant="ghost" size="sm" icon="x-mark" wire:click="cancelDelete">
                    <span class="sr-only">{{ __('Close') }}</span>
                </flux:button>
            </div>

            <p class="text-sm text-[#8fb3d9]">
                {{ __('Are you sure you want to delete the warehouse ":name"?', ['name' => $deleteContextName]) }}
            </p>

            @error('delete')
                <p class="text-sm text-red-300">{{ $message }}</p>
            @enderror

            <div class="flex items-center justify-end gap-3">
                <flux:button type="button" variant="ghost" wire:click="cancelDelete" wire:loading.attr="disabled"
                    wire:target="deleteWarehouse">
                    {{ __('Cancel') }}
                </flux:button>

                <flux:button type="button" variant="danger" wire:click="deleteWarehouse" wire:loading.attr="disabled"
                    wire:target="deleteWarehouse">
                    <span wire:loading.remove wire:target="deleteWarehouse">
                        {{ __('Delete Warehouse') }}
                    </span>
                    <span wire:loading wire:target="deleteWarehouse" class="inline-flex items-center gap-2">
                        <flux:icon.loading class="h-4 w-4" />
                        {{ __('Deleting...') }}
                    </span>
                </flux:button>
            </div>
        </div>
    </flux:modal>
</x-page-body>
