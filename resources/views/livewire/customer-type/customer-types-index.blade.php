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
            <x-table-navbar-title title="{{ __('Customer Type') }}"
                subtitle="{{ __('Maintain customer type names used for customer grouping.') }}">
            </x-table-navbar-title>

            <x-table-navbar-search-button>
                <x-table-navbar-per-page-option modellive="perPage" :pageOptions="$perPageOptions">
                </x-table-navbar-per-page-option>

                <flux:input wire:model.live.debounce.400ms="search" :placeholder="__('Search customer types...')"
                    icon="magnifying-glass" class="sm:w-64" />

                <flux:button type="button" variant="primary" wire:click="openCreateModal">
                    {{ __('Add Customer Type') }}
                </flux:button>
            </x-table-navbar-search-button>
        </x-table-navbar>

        @php
            $dtHead = ['Name', 'Customers Used', 'Actions'];
        @endphp

        <x-table-custom>
            <x-table-head :head="$dtHead"></x-table-head>

            <x-table-body :items="$customerTypes" row-view="livewire.customer-type.partials.customer-type-row"
                :columns="3" item-key="customerType" empty-message="{{ __('No customer types found.') }}" />
        </x-table-custom>

        <div class="pt-2">
            {{ $customerTypes->links() }}
        </div>
    </x-table-section>

    <x-modal name="create-customer-type-modal" class="max-w-2xl" title="{{ __('Add Customer Type') }}"
        subtitle="{{ __('Create a customer type for customer grouping.') }}" wire:model="showCreateModal"
        wire-target="insertCustomerType" :createFeedback="$createFeedback" close-action="closeCreateModal"
        loading-message="{{ __('Saving customer type...') }}">
        <form wire:submit.prevent="insertCustomerType" class="space-y-4">
            <flux:input wire:model.defer="createForm.name" :label="__('Name')" type="text" required autofocus />

            <div class="flex items-center justify-end gap-3 pt-2">
                <flux:button type="button" variant="ghost" wire:click="closeCreateModal" wire:loading.attr="disabled"
                    wire:target="insertCustomerType">
                    {{ __('Cancel') }}
                </flux:button>

                <flux:button type="submit" variant="primary" wire:loading.attr="disabled"
                    wire:target="insertCustomerType">
                    <span wire:loading.remove wire:target="insertCustomerType">
                        {{ __('Create Customer Type') }}
                    </span>
                    <span wire:loading wire:target="insertCustomerType" class="inline-flex items-center gap-2">
                        <flux:icon.loading class="h-4 w-4" />
                        {{ __('Saving...') }}
                    </span>
                </flux:button>
            </div>
        </form>
    </x-modal>

    <x-modal name="edit-customer-type-modal" class="max-w-2xl" title="{{ __('Edit Customer Type') }}"
        subtitle="{{ __('Update customer type name.') }}" wire:model="showEditModal"
        wire-target="updateCustomerType" :createFeedback="$editFeedback" close-action="cancelEditing"
        loading-message="{{ __('Updating customer type...') }}">
        <form wire:submit.prevent="updateCustomerType" class="space-y-4">
            <flux:input wire:model.defer="editForm.name" :label="__('Name')" type="text" required />

            <div class="flex items-center justify-end gap-3 pt-2">
                <flux:button type="button" variant="ghost" wire:click="cancelEditing" wire:loading.attr="disabled"
                    wire:target="updateCustomerType">
                    {{ __('Cancel') }}
                </flux:button>

                <flux:button type="submit" variant="primary" wire:loading.attr="disabled"
                    wire:target="updateCustomerType">
                    <span wire:loading.remove wire:target="updateCustomerType">
                        {{ __('Update Customer Type') }}
                    </span>
                    <span wire:loading wire:target="updateCustomerType" class="inline-flex items-center gap-2">
                        <flux:icon.loading class="h-4 w-4" />
                        {{ __('Saving...') }}
                    </span>
                </flux:button>
            </div>
        </form>
    </x-modal>

    <flux:modal name="delete-customer-type-modal" wire:model="showDeleteModal" focusable class="max-w-md"
        @close="$wire.cancelDelete()">
        <div class="relative space-y-6">
            <div wire:loading wire:target="deleteCustomerType"
                class="absolute inset-0 z-20 flex items-center justify-center rounded-xl bg-[#0d1a18]/70">
                <span class="inline-flex items-center gap-2 text-sm font-semibold text-[#d6c172]">
                    <flux:icon.loading class="h-5 w-5" />
                    {{ __('Deleting customer type...') }}
                </span>
            </div>

            <div class="flex items-start justify-between gap-4">
                <div class="space-y-1">
                    <flux:heading size="lg">{{ __('Delete Customer Type') }}</flux:heading>
                    <flux:text variant="subtle">
                        {{ __('This action cannot be undone.') }}
                    </flux:text>
                </div>

                <flux:button variant="ghost" size="sm" icon="x-mark" wire:click="cancelDelete">
                    <span class="sr-only">{{ __('Close') }}</span>
                </flux:button>
            </div>

            <p class="text-sm text-[#a9c2bd]">
                {{ __('Are you sure you want to delete the customer type ":name"?', ['name' => $deleteContextName]) }}
            </p>

            @error('delete')
                <p class="text-sm text-red-300">{{ $message }}</p>
            @enderror

            <div class="flex items-center justify-end gap-3">
                <flux:button type="button" variant="ghost" wire:click="cancelDelete" wire:loading.attr="disabled"
                    wire:target="deleteCustomerType">
                    {{ __('Cancel') }}
                </flux:button>

                <flux:button type="button" variant="danger" wire:click="deleteCustomerType"
                    wire:loading.attr="disabled" wire:target="deleteCustomerType">
                    <span wire:loading.remove wire:target="deleteCustomerType">
                        {{ __('Delete Customer Type') }}
                    </span>
                    <span wire:loading wire:target="deleteCustomerType" class="inline-flex items-center gap-2">
                        <flux:icon.loading class="h-4 w-4" />
                        {{ __('Deleting...') }}
                    </span>
                </flux:button>
            </div>
        </div>
    </flux:modal>
</x-page-body>
