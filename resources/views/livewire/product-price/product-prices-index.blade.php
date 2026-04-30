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
            <x-table-navbar-title title="{{ __('Product Price') }}"
                subtitle="{{ __('Maintain product prices and active selling price records.') }}">
            </x-table-navbar-title>

            <x-table-navbar-search-button>
                <x-table-navbar-per-page-option modellive="perPage" :pageOptions="$perPageOptions">
                </x-table-navbar-per-page-option>

                <flux:select wire:model.live="filters.product_id" class="sm:w-56">
                    <option value="">{{ __('All Products') }}</option>
                    @foreach ($productOptions as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </flux:select>

                <flux:select wire:model.live="filters.is_active" class="sm:w-40">
                    <option value="">{{ __('All Status') }}</option>
                    <option value="1">{{ __('Active') }}</option>
                    <option value="0">{{ __('Inactive') }}</option>
                </flux:select>

                <flux:input wire:model.live.debounce.400ms="search" :placeholder="__('Search products...')"
                    icon="magnifying-glass" class="sm:w-64" />

                <flux:button type="button" variant="primary" wire:click="openCreateModal">
                    {{ __('Add Price') }}
                </flux:button>
            </x-table-navbar-search-button>
        </x-table-navbar>

        @php
            $dtHead = ['Product', 'Price', 'Status', 'Actions'];
        @endphp

        <x-table-custom>
            <x-table-head :head="$dtHead"></x-table-head>

            <x-table-body :items="$productPrices" row-view="livewire.product-price.partials.product-price-row"
                :columns="4" item-key="productPrice" empty-message="{{ __('No product prices found.') }}" />
        </x-table-custom>

        <div class="pt-2">
            {{ $productPrices->links() }}
        </div>
    </x-table-section>

    <x-modal name="create-product-price-modal" class="max-w-2xl" title="{{ __('Add Product Price') }}"
        subtitle="{{ __('Create a price record for a product.') }}" wire:model="showCreateModal"
        wire-target="insertProductPrice" :createFeedback="$createFeedback" close-action="closeCreateModal"
        loading-message="{{ __('Saving product price...') }}">
        <form wire:submit.prevent="insertProductPrice" class="space-y-4">
            <flux:select wire:model.defer="createForm.product_id" :label="__('Product')" required autofocus>
                <option value="">{{ __('Select product') }}</option>
                @foreach ($productOptions as $product)
                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                @endforeach
            </flux:select>

            <div class="grid gap-4 md:grid-cols-2">
                <flux:input wire:model.defer="createForm.price" :label="__('Price')" type="text" inputmode="decimal"
                    required />

                <flux:select wire:model.defer="createForm.is_active" :label="__('Status')" required>
                    <option value="1">{{ __('Active') }}</option>
                    <option value="0">{{ __('Inactive') }}</option>
                </flux:select>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <flux:button type="button" variant="ghost" wire:click="closeCreateModal" wire:loading.attr="disabled"
                    wire:target="insertProductPrice">
                    {{ __('Cancel') }}
                </flux:button>

                <flux:button type="submit" variant="primary" wire:loading.attr="disabled"
                    wire:target="insertProductPrice">
                    <span wire:loading.remove wire:target="insertProductPrice">
                        {{ __('Create Price') }}
                    </span>
                    <span wire:loading wire:target="insertProductPrice" class="inline-flex items-center gap-2">
                        <flux:icon.loading class="h-4 w-4" />
                        {{ __('Saving...') }}
                    </span>
                </flux:button>
            </div>
        </form>
    </x-modal>

    <x-modal name="edit-product-price-modal" class="max-w-2xl" title="{{ __('Edit Product Price') }}"
        subtitle="{{ __('Update product price and active status.') }}" wire:model="showEditModal"
        wire-target="updateProductPrice" :createFeedback="$editFeedback" close-action="cancelEditing"
        loading-message="{{ __('Updating product price...') }}">
        <form wire:submit.prevent="updateProductPrice" class="space-y-4">
            <flux:select wire:model.defer="editForm.product_id" :label="__('Product')" required>
                <option value="">{{ __('Select product') }}</option>
                @foreach ($productOptions as $product)
                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                @endforeach
            </flux:select>

            <div class="grid gap-4 md:grid-cols-2">
                <flux:input wire:model.defer="editForm.price" :label="__('Price')" type="text" inputmode="decimal"
                    required />

                <flux:select wire:model.defer="editForm.is_active" :label="__('Status')" required>
                    <option value="1">{{ __('Active') }}</option>
                    <option value="0">{{ __('Inactive') }}</option>
                </flux:select>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <flux:button type="button" variant="ghost" wire:click="cancelEditing" wire:loading.attr="disabled"
                    wire:target="updateProductPrice">
                    {{ __('Cancel') }}
                </flux:button>

                <flux:button type="submit" variant="primary" wire:loading.attr="disabled"
                    wire:target="updateProductPrice">
                    <span wire:loading.remove wire:target="updateProductPrice">
                        {{ __('Update Price') }}
                    </span>
                    <span wire:loading wire:target="updateProductPrice" class="inline-flex items-center gap-2">
                        <flux:icon.loading class="h-4 w-4" />
                        {{ __('Saving...') }}
                    </span>
                </flux:button>
            </div>
        </form>
    </x-modal>

    <flux:modal name="delete-product-price-modal" wire:model="showDeleteModal" focusable class="max-w-md"
        @close="$wire.cancelDelete()">
        <div class="relative space-y-6">
            <div wire:loading wire:target="deleteProductPrice"
                class="absolute inset-0 z-20 flex items-center justify-center rounded-xl bg-[#0d1a18]/70">
                <span class="inline-flex items-center gap-2 text-sm font-semibold text-[#d6c172]">
                    <flux:icon.loading class="h-5 w-5" />
                    {{ __('Deleting product price...') }}
                </span>
            </div>

            <div class="flex items-start justify-between gap-4">
                <div class="space-y-1">
                    <flux:heading size="lg">{{ __('Delete Product Price') }}</flux:heading>
                    <flux:text variant="subtle">
                        {{ __('This action cannot be undone.') }}
                    </flux:text>
                </div>

                <flux:button variant="ghost" size="sm" icon="x-mark" wire:click="cancelDelete">
                    <span class="sr-only">{{ __('Close') }}</span>
                </flux:button>
            </div>

            <p class="text-sm text-[#a9c2bd]">
                {{ __('Are you sure you want to delete the price for ":name"?', ['name' => $deleteContextName]) }}
            </p>

            @error('delete')
                <p class="text-sm text-red-300">{{ $message }}</p>
            @enderror

            <div class="flex items-center justify-end gap-3">
                <flux:button type="button" variant="ghost" wire:click="cancelDelete" wire:loading.attr="disabled"
                    wire:target="deleteProductPrice">
                    {{ __('Cancel') }}
                </flux:button>

                <flux:button type="button" variant="danger" wire:click="deleteProductPrice"
                    wire:loading.attr="disabled" wire:target="deleteProductPrice">
                    <span wire:loading.remove wire:target="deleteProductPrice">
                        {{ __('Delete Price') }}
                    </span>
                    <span wire:loading wire:target="deleteProductPrice" class="inline-flex items-center gap-2">
                        <flux:icon.loading class="h-4 w-4" />
                        {{ __('Deleting...') }}
                    </span>
                </flux:button>
            </div>
        </div>
    </flux:modal>
</x-page-body>
