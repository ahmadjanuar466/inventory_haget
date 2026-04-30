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
            <x-table-navbar-title title="{{ __('List Product') }}"
                subtitle="{{ __('Maintain product data, category, unit, price, and stock configuration.') }}">
            </x-table-navbar-title>

            <x-table-navbar-search-button>
                <x-table-navbar-per-page-option modellive="perPage" :pageOptions="$perPageOptions">
                </x-table-navbar-per-page-option>

                <flux:select wire:model.live="filters.category_id" class="sm:w-52">
                    <option value="">{{ __('All Categories') }}</option>
                    @foreach ($categoryOptions as $categoryOption)
                        <option value="{{ $categoryOption->id }}">{{ $categoryOption->name }}</option>
                    @endforeach
                </flux:select>

                <flux:select wire:model.live="filters.units_id" class="sm:w-44">
                    <option value="">{{ __('All Units') }}</option>
                    @foreach ($unitOptions as $unitOption)
                        <option value="{{ $unitOption->id }}">{{ $unitOption->name }}</option>
                    @endforeach
                </flux:select>

                <flux:input wire:model.live.debounce.400ms="search" :placeholder="__('Search products...')"
                    icon="magnifying-glass" class="sm:w-64" />

                <flux:button type="button" variant="primary" wire:click="openCreateModal">
                    {{ __('Add Product') }}
                </flux:button>
            </x-table-navbar-search-button>
        </x-table-navbar>

        @php
            $dtHead = ['SKU', 'Name', 'Category', 'Unit', 'Cost', 'Sell', 'Status', 'Actions'];
        @endphp

        <x-table-custom>
            <x-table-head :head="$dtHead"></x-table-head>

            <x-table-body :items="$products" row-view="livewire.product.partials.product-row" :columns="8"
                item-key="product" empty-message="{{ __('No products found.') }}" />
        </x-table-custom>

        <div class="pt-2">
            {{ $products->links() }}
        </div>
    </x-table-section>

    <x-modal name="create-product-modal" class="max-w-4xl" title="{{ __('Add Product') }}"
        subtitle="{{ __('Create a product with category, unit, price, and stock settings.') }}"
        wire:model="showCreateModal" wire-target="insertProduct" :createFeedback="$createFeedback"
        close-action="closeCreateModal" loading-message="{{ __('Saving product...') }}">
        <form wire:submit.prevent="insertProduct" class="space-y-4">
            <div class="grid gap-4 md:grid-cols-2">
                <flux:input wire:model.defer="createForm.sku" :label="__('SKU')" type="text" required autofocus />
                <flux:input wire:model.defer="createForm.name" :label="__('Name')" type="text" required />

                <flux:select wire:model.defer="createForm.category_id" :label="__('Category')" required>
                    <option value="">{{ __('Choose category') }}</option>
                    @foreach ($categoryOptions as $categoryOption)
                        <option value="{{ $categoryOption->id }}">{{ $categoryOption->name }}</option>
                    @endforeach
                </flux:select>

                <flux:select wire:model.defer="createForm.units_id" :label="__('Unit')" required>
                    <option value="">{{ __('Choose unit') }}</option>
                    @foreach ($unitOptions as $unitOption)
                        <option value="{{ $unitOption->id }}">{{ $unitOption->name }}</option>
                    @endforeach
                </flux:select>

                <flux:input wire:model.defer="createForm.cost_price" :label="__('Cost Price')" type="number"
                    min="0" step="0.01" />
                <flux:input wire:model.defer="createForm.sell_price" :label="__('Sell Price')" type="number"
                    min="0" step="0.01" />
                <flux:input wire:model.defer="createForm.min_stock" :label="__('Minimum Stock')" type="number"
                    min="0" step="0.01" />

                <flux:select wire:model.defer="createForm.track_stock" :label="__('Track Stock')" required>
                    <option value="0">{{ __('No') }}</option>
                    <option value="1">{{ __('Yes') }}</option>
                </flux:select>

                <flux:select wire:model.defer="createForm.has_expiry" :label="__('Has Expiry')" required>
                    <option value="0">{{ __('No') }}</option>
                    <option value="1">{{ __('Yes') }}</option>
                </flux:select>

                <flux:select wire:model.defer="createForm.is_active" :label="__('Status')" required>
                    <option value="1">{{ __('Active') }}</option>
                    <option value="0">{{ __('Inactive') }}</option>
                </flux:select>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <flux:button type="button" variant="ghost" wire:click="closeCreateModal" wire:loading.attr="disabled"
                    wire:target="insertProduct">
                    {{ __('Cancel') }}
                </flux:button>

                <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="insertProduct">
                    <span wire:loading.remove wire:target="insertProduct">
                        {{ __('Create Product') }}
                    </span>
                    <span wire:loading wire:target="insertProduct" class="inline-flex items-center gap-2">
                        <flux:icon.loading class="h-4 w-4" />
                        {{ __('Saving...') }}
                    </span>
                </flux:button>
            </div>
        </form>
    </x-modal>

    <x-modal name="edit-product-modal" class="max-w-4xl" title="{{ __('Edit Product') }}"
        subtitle="{{ __('Update product data, category, unit, price, and stock settings.') }}"
        wire:model="showEditModal" wire-target="updateProduct" :createFeedback="$editFeedback"
        close-action="cancelEditing" loading-message="{{ __('Updating product...') }}">
        <form wire:submit.prevent="updateProduct" class="space-y-4">
            <div class="grid gap-4 md:grid-cols-2">
                <flux:input wire:model.defer="editForm.sku" :label="__('SKU')" type="text" required />
                <flux:input wire:model.defer="editForm.name" :label="__('Name')" type="text" required />

                <flux:select wire:model.defer="editForm.category_id" :label="__('Category')" required>
                    <option value="">{{ __('Choose category') }}</option>
                    @foreach ($categoryOptions as $categoryOption)
                        <option value="{{ $categoryOption->id }}">{{ $categoryOption->name }}</option>
                    @endforeach
                </flux:select>

                <flux:select wire:model.defer="editForm.units_id" :label="__('Unit')" required>
                    <option value="">{{ __('Choose unit') }}</option>
                    @foreach ($unitOptions as $unitOption)
                        <option value="{{ $unitOption->id }}">{{ $unitOption->name }}</option>
                    @endforeach
                </flux:select>

                <flux:input wire:model.defer="editForm.cost_price" :label="__('Cost Price')" type="number"
                    min="0" step="0.01" />
                <flux:input wire:model.defer="editForm.sell_price" :label="__('Sell Price')" type="number"
                    min="0" step="0.01" />
                <flux:input wire:model.defer="editForm.min_stock" :label="__('Minimum Stock')" type="number"
                    min="0" step="0.01" />

                <flux:select wire:model.defer="editForm.track_stock" :label="__('Track Stock')" required>
                    <option value="0">{{ __('No') }}</option>
                    <option value="1">{{ __('Yes') }}</option>
                </flux:select>

                <flux:select wire:model.defer="editForm.has_expiry" :label="__('Has Expiry')" required>
                    <option value="0">{{ __('No') }}</option>
                    <option value="1">{{ __('Yes') }}</option>
                </flux:select>

                <flux:select wire:model.defer="editForm.is_active" :label="__('Status')" required>
                    <option value="1">{{ __('Active') }}</option>
                    <option value="0">{{ __('Inactive') }}</option>
                </flux:select>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <flux:button type="button" variant="ghost" wire:click="cancelEditing" wire:loading.attr="disabled"
                    wire:target="updateProduct">
                    {{ __('Cancel') }}
                </flux:button>

                <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="updateProduct">
                    <span wire:loading.remove wire:target="updateProduct">
                        {{ __('Update Product') }}
                    </span>
                    <span wire:loading wire:target="updateProduct" class="inline-flex items-center gap-2">
                        <flux:icon.loading class="h-4 w-4" />
                        {{ __('Saving...') }}
                    </span>
                </flux:button>
            </div>
        </form>
    </x-modal>

    <flux:modal name="delete-product-modal" wire:model="showDeleteModal" focusable class="max-w-md"
        @close="$wire.cancelDelete()">
        <div class="relative space-y-6">
            <div wire:loading wire:target="deleteProduct"
                class="absolute inset-0 z-20 flex items-center justify-center rounded-xl bg-[#0d1a18]/70">
                <span class="inline-flex items-center gap-2 text-sm font-semibold text-[#d6c172]">
                    <flux:icon.loading class="h-5 w-5" />
                    {{ __('Deleting product...') }}
                </span>
            </div>

            <div class="flex items-start justify-between gap-4">
                <div class="space-y-1">
                    <flux:heading size="lg">{{ __('Delete Product') }}</flux:heading>
                    <flux:text variant="subtle">
                        {{ __('This action cannot be undone.') }}
                    </flux:text>
                </div>

                <flux:button variant="ghost" size="sm" icon="x-mark" wire:click="cancelDelete">
                    <span class="sr-only">{{ __('Close') }}</span>
                </flux:button>
            </div>

            <p class="text-sm text-[#a9c2bd]">
                {{ __('Are you sure you want to delete the product ":name"?', ['name' => $deleteContextName]) }}
            </p>

            @error('delete')
                <p class="text-sm text-red-300">{{ $message }}</p>
            @enderror

            <div class="flex items-center justify-end gap-3">
                <flux:button type="button" variant="ghost" wire:click="cancelDelete" wire:loading.attr="disabled"
                    wire:target="deleteProduct">
                    {{ __('Cancel') }}
                </flux:button>

                <flux:button type="button" variant="danger" wire:click="deleteProduct" wire:loading.attr="disabled"
                    wire:target="deleteProduct">
                    <span wire:loading.remove wire:target="deleteProduct">
                        {{ __('Delete Product') }}
                    </span>
                    <span wire:loading wire:target="deleteProduct" class="inline-flex items-center gap-2">
                        <flux:icon.loading class="h-4 w-4" />
                        {{ __('Deleting...') }}
                    </span>
                </flux:button>
            </div>
        </div>
    </flux:modal>
</x-page-body>
