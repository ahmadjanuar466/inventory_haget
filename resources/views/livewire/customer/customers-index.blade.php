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
            <x-table-navbar-title title="{{ __('List Customer') }}"
                subtitle="{{ __('Maintain customer codes, types, contact details, and status.') }}">
            </x-table-navbar-title>

            <x-table-navbar-search-button>
                <x-table-navbar-per-page-option modellive="perPage" :pageOptions="$perPageOptions">
                </x-table-navbar-per-page-option>

                <flux:select wire:model.live="filters.customer_type_id" class="sm:w-52">
                    <option value="">{{ __('All Customer Types') }}</option>
                    @foreach ($customerTypeOptions as $customerType)
                        <option value="{{ $customerType->id }}">{{ $customerType->name }}</option>
                    @endforeach
                </flux:select>

                <flux:select wire:model.live="filters.is_active" class="sm:w-40">
                    <option value="">{{ __('All Status') }}</option>
                    <option value="1">{{ __('Active') }}</option>
                    <option value="0">{{ __('Inactive') }}</option>
                </flux:select>

                <flux:input wire:model.live.debounce.400ms="search" :placeholder="__('Search customers...')"
                    icon="magnifying-glass" class="sm:w-64" />

                <flux:button type="button" variant="primary" wire:click="openCreateModal">
                    {{ __('Add Customer') }}
                </flux:button>
            </x-table-navbar-search-button>
        </x-table-navbar>

        @php
            $dtHead = ['Code', 'Customer', 'Type', 'Contact', 'Status', 'Actions'];
        @endphp

        <x-table-custom>
            <x-table-head :head="$dtHead"></x-table-head>

            <x-table-body :items="$customers" row-view="livewire.customer.partials.customer-row" :columns="6"
                item-key="customer" empty-message="{{ __('No customers found.') }}" />
        </x-table-custom>

        <div class="pt-2">
            {{ $customers->links() }}
        </div>
    </x-table-section>

    <x-modal name="create-customer-modal" class="max-w-2xl" title="{{ __('Add Customer') }}"
        subtitle="{{ __('Create a customer with type and contact details.') }}" wire:model="showCreateModal"
        wire-target="insertCustomer" :createFeedback="$createFeedback" close-action="closeCreateModal"
        loading-message="{{ __('Saving customer...') }}">
        <form wire:submit.prevent="insertCustomer" class="space-y-4">
            <div class="grid gap-4 md:grid-cols-2">
                <flux:select wire:model.defer="createForm.customer_type_id" :label="__('Customer Type')" required>
                    <option value="">{{ __('Select customer type') }}</option>
                    @foreach ($customerTypeOptions as $customerType)
                        <option value="{{ $customerType->id }}">{{ $customerType->name }}</option>
                    @endforeach
                </flux:select>

                <flux:input wire:model.defer="createForm.name" :label="__('Name')" type="text" required autofocus />
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <flux:input wire:model.defer="createForm.phone" :label="__('Phone')" type="text" />
                <flux:input wire:model.defer="createForm.email" :label="__('Email')" type="email" />
            </div>

            <flux:textarea wire:model.defer="createForm.address" :label="__('Address')" rows="3" />

            <flux:select wire:model.defer="createForm.is_active" :label="__('Status')" required>
                <option value="1">{{ __('Active') }}</option>
                <option value="0">{{ __('Inactive') }}</option>
            </flux:select>

            <div class="flex items-center justify-end gap-3 pt-2">
                <flux:button type="button" variant="ghost" wire:click="closeCreateModal" wire:loading.attr="disabled"
                    wire:target="insertCustomer">
                    {{ __('Cancel') }}
                </flux:button>

                <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="insertCustomer">
                    <span wire:loading.remove wire:target="insertCustomer">
                        {{ __('Create Customer') }}
                    </span>
                    <span wire:loading wire:target="insertCustomer" class="inline-flex items-center gap-2">
                        <flux:icon.loading class="h-4 w-4" />
                        {{ __('Saving...') }}
                    </span>
                </flux:button>
            </div>
        </form>
    </x-modal>

    <x-modal name="edit-customer-modal" class="max-w-2xl" title="{{ __('Edit Customer') }}"
        subtitle="{{ __('Update customer identity and contact details.') }}" wire:model="showEditModal"
        wire-target="updateCustomer" :createFeedback="$editFeedback" close-action="cancelEditing"
        loading-message="{{ __('Updating customer...') }}">
        <form wire:submit.prevent="updateCustomer" class="space-y-4">
            <div class="grid gap-4 md:grid-cols-2">
                <flux:input wire:model.defer="editForm.code" :label="__('Code')" type="text" readonly required />
                <flux:input wire:model.defer="editForm.name" :label="__('Name')" type="text" required />
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <flux:select wire:model.defer="editForm.customer_type_id" :label="__('Customer Type')" required>
                    <option value="">{{ __('Select customer type') }}</option>
                    @foreach ($customerTypeOptions as $customerType)
                        <option value="{{ $customerType->id }}">{{ $customerType->name }}</option>
                    @endforeach
                </flux:select>

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
                    wire:target="updateCustomer">
                    {{ __('Cancel') }}
                </flux:button>

                <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="updateCustomer">
                    <span wire:loading.remove wire:target="updateCustomer">
                        {{ __('Update Customer') }}
                    </span>
                    <span wire:loading wire:target="updateCustomer" class="inline-flex items-center gap-2">
                        <flux:icon.loading class="h-4 w-4" />
                        {{ __('Saving...') }}
                    </span>
                </flux:button>
            </div>
        </form>
    </x-modal>

    <flux:modal name="delete-customer-modal" wire:model="showDeleteModal" focusable class="max-w-md"
        @close="$wire.cancelDelete()">
        <div class="relative space-y-6">
            <div wire:loading wire:target="deleteCustomer"
                class="absolute inset-0 z-20 flex items-center justify-center rounded-xl bg-[#0d1a18]/70">
                <span class="inline-flex items-center gap-2 text-sm font-semibold text-[#d6c172]">
                    <flux:icon.loading class="h-5 w-5" />
                    {{ __('Deleting customer...') }}
                </span>
            </div>

            <div class="flex items-start justify-between gap-4">
                <div class="space-y-1">
                    <flux:heading size="lg">{{ __('Delete Customer') }}</flux:heading>
                    <flux:text variant="subtle">
                        {{ __('This action cannot be undone.') }}
                    </flux:text>
                </div>

                <flux:button variant="ghost" size="sm" icon="x-mark" wire:click="cancelDelete">
                    <span class="sr-only">{{ __('Close') }}</span>
                </flux:button>
            </div>

            <p class="text-sm text-[#a9c2bd]">
                {{ __('Are you sure you want to delete the customer ":name"?', ['name' => $deleteContextName]) }}
            </p>

            @error('delete')
                <p class="text-sm text-red-300">{{ $message }}</p>
            @enderror

            <div class="flex items-center justify-end gap-3">
                <flux:button type="button" variant="ghost" wire:click="cancelDelete" wire:loading.attr="disabled"
                    wire:target="deleteCustomer">
                    {{ __('Cancel') }}
                </flux:button>

                <flux:button type="button" variant="danger" wire:click="deleteCustomer" wire:loading.attr="disabled"
                    wire:target="deleteCustomer">
                    <span wire:loading.remove wire:target="deleteCustomer">
                        {{ __('Delete Customer') }}
                    </span>
                    <span wire:loading wire:target="deleteCustomer" class="inline-flex items-center gap-2">
                        <flux:icon.loading class="h-4 w-4" />
                        {{ __('Deleting...') }}
                    </span>
                </flux:button>
            </div>
        </div>
    </flux:modal>
</x-page-body>
