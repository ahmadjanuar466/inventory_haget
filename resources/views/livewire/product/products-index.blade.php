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
                subtitle="{{ __('Maintain product SKU, category, unit, and stock behavior.') }}">
            </x-table-navbar-title>

            <x-table-navbar-search-button>
                <x-table-navbar-per-page-option modellive="perPage" :pageOptions="$perPageOptions">
                </x-table-navbar-per-page-option>

                <flux:select wire:model.live="filters.category_id" class="sm:w-52">
                    <option value="">{{ __('All Categories') }}</option>
                    @foreach ($categoryOptions as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </flux:select>

                <flux:select wire:model.live="filters.units_id" class="sm:w-44">
                    <option value="">{{ __('All Units') }}</option>
                    @foreach ($unitOptions as $unit)
                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
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
                    {{ __('Add Product') }}
                </flux:button>
            </x-table-navbar-search-button>
        </x-table-navbar>

        @php
            $dtHead = ['SKU', 'Product', 'Category', 'Unit', 'Status', 'Actions'];
        @endphp

        <x-table-custom>
            <x-table-head :head="$dtHead"></x-table-head>

            <x-table-body :items="$products" row-view="livewire.product.partials.product-row" :columns="6"
                item-key="product" empty-message="{{ __('No products found.') }}" />
        </x-table-custom>

        <div class="pt-2">
            {{ $products->links() }}
        </div>
    </x-table-section>

    <x-modal name="create-product-modal" class="max-w-3xl" title="{{ __('Add Product') }}"
        subtitle="{{ __('Create a product with category, stock settings, and unit conversions.') }}"
        wire:model="showCreateModal" wire-target="createProduct" :createFeedback="$createFeedback"
        close-action="closeCreateModal" loading-message="{{ __('Saving product...') }}">
        <form wire:submit.prevent="createProduct" class="space-y-4">
            <div class="grid gap-4 md:grid-cols-2">
                <flux:input wire:model.defer="createForm.sku" :label="__('SKU')" type="text" required autofocus />
                <flux:input wire:model.defer="createForm.name" :label="__('Name')" type="text" required />
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <flux:select wire:model.defer="createForm.category_id" :label="__('Category')" required>
                    <option value="">{{ __('Select category') }}</option>
                    @foreach ($categoryOptions as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </flux:select>

                <flux:select wire:model.live="createForm.units_id" :label="__('Base Unit')" required>
                    <option value="">{{ __('Select unit') }}</option>
                    @foreach ($unitOptions as $unit)
                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                    @endforeach
                </flux:select>
            </div>

            <div class="grid gap-4 md:grid-cols-3">
                <flux:input wire:model.defer="createForm.min_stock" :label="__('Minimum Stock')" type="number"
                    step="0.01" min="0" />

                <flux:select wire:model.defer="createForm.track_stock" :label="__('Track Stock')" required>
                    <option value="1">{{ __('Yes') }}</option>
                    <option value="0">{{ __('No') }}</option>
                </flux:select>

                <flux:select wire:model.defer="createForm.has_expiry" :label="__('Has Expiry')" required>
                    <option value="1">{{ __('Yes') }}</option>
                    <option value="0">{{ __('No') }}</option>
                </flux:select>
            </div>

            <flux:select wire:model.defer="createForm.is_active" :label="__('Status')" required>
                <option value="1">{{ __('Active') }}</option>
                <option value="0">{{ __('Inactive') }}</option>
            </flux:select>

            <div class="space-y-4 rounded-xl border border-[#142a28]/70 bg-[#10211f]/40 p-4">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div class="space-y-1">
                        <flux:heading size="lg">{{ __('Additional Units') }}</flux:heading>
                        <flux:text variant="subtle">
                            {{ __('Base unit is saved automatically as 1 unit. Conversion qty can be left blank to use 1, or filled manually, for example 1 Box = 12 Pcs.') }}
                        </flux:text>
                    </div>

                    @if ($createForm['units_id'] !== '')
                        <flux:button type="button" variant="ghost" wire:click="addProductUnitRow('createForm')">
                            {{ __('Add Conversion Unit') }}
                        </flux:button>
                    @endif
                </div>

                @php
                    $createBaseUnit = $unitOptions->firstWhere('id', (int) ($createForm['units_id'] ?? 0));
                @endphp

                @if ($createBaseUnit)
                    <div class="rounded-lg border border-[#142a28]/60 bg-[#1c3432]/70 px-4 py-3 text-sm text-[#a9c2bd]">
                        {{ __('Base unit will be saved as 1 :unit. Leave conversion qty empty to use 1, or fill a custom value.', ['unit' => $createBaseUnit->name]) }}
                    </div>
                @else
                    <div class="rounded-lg border border-[#142a28]/60 bg-[#1c3432]/70 px-4 py-3 text-sm text-[#a9c2bd]">
                        {{ __('Select a base unit first before adding unit conversions.') }}
                    </div>
                @endif

                @foreach ($createForm['product_units'] as $index => $productUnit)
                    @php
                        $selectedUnit = $unitOptions->firstWhere('id', (int) ($productUnit['unit_id'] ?? 0));
                    @endphp

                    <div wire:key="create-product-unit-{{ $index }}"
                        class="space-y-3 rounded-lg border border-[#142a28]/60 bg-[#1c3432]/50 p-4">
                        <div class="grid gap-4 md:grid-cols-[1.5fr_1fr_1fr_auto]">
                            <flux:select wire:model.defer="createForm.product_units.{{ $index }}.unit_id"
                                :label="__('Additional Unit')">
                                <option value="">{{ __('Select unit') }}</option>
                                @foreach ($unitOptions as $unit)
                                    @if ((string) $unit->id !== (string) ($createForm['units_id'] ?? ''))
                                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                    @endif
                                @endforeach
                            </flux:select>

                            <flux:input wire:model.defer="createForm.product_units.{{ $index }}.conversion_qty"
                                :label="__('Qty for Selected Unit')" type="text" inputmode="decimal"
                                placeholder="{{ __('Leave blank for 1, or example: 12') }}" />

                            <flux:select wire:model.defer="createForm.product_units.{{ $index }}.is_active"
                                :label="__('Status')" required>
                                <option value="1">{{ __('Active') }}</option>
                                <option value="0">{{ __('Inactive') }}</option>
                            </flux:select>

                            <div class="flex items-end">
                                <flux:button type="button" variant="danger" wire:click="removeProductUnitRow('createForm', {{ $index }})">
                                    {{ __('Remove') }}
                                </flux:button>
                            </div>
                        </div>

                        @if ($createBaseUnit && $selectedUnit && ($productUnit['conversion_qty'] ?? '') !== '')
                            <p class="text-sm text-[#a9c2bd]">
                                {{ __('1 :base = :qty :unit', ['base' => $createBaseUnit->name, 'qty' => $productUnit['conversion_qty'], 'unit' => $selectedUnit->name]) }}
                            </p>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <flux:button type="button" variant="ghost" wire:click="closeCreateModal" wire:loading.attr="disabled"
                    wire:target="createProduct">
                    {{ __('Cancel') }}
                </flux:button>

                <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="createProduct">
                    <span wire:loading.remove wire:target="createProduct">
                        {{ __('Create Product') }}
                    </span>
                    <span wire:loading wire:target="createProduct" class="inline-flex items-center gap-2">
                        <flux:icon.loading class="h-4 w-4" />
                        {{ __('Saving...') }}
                    </span>
                </flux:button>
            </div>
        </form>
    </x-modal>

    <x-modal name="edit-product-modal" class="max-w-3xl" title="{{ __('Edit Product') }}"
        subtitle="{{ __('Update product identity, stock settings, and unit conversions.') }}"
        wire:model="showEditModal" wire-target="updateProduct" :createFeedback="$editFeedback"
        close-action="cancelEditing" loading-message="{{ __('Updating product...') }}">
        <form wire:submit.prevent="updateProduct" class="space-y-4">
            <div class="grid gap-4 md:grid-cols-2">
                <flux:input wire:model.defer="editForm.sku" :label="__('SKU')" type="text" required />
                <flux:input wire:model.defer="editForm.name" :label="__('Name')" type="text" required />
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <flux:select wire:model.defer="editForm.category_id" :label="__('Category')" required>
                    <option value="">{{ __('Select category') }}</option>
                    @foreach ($categoryOptions as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </flux:select>

                <flux:select wire:model.live="editForm.units_id" :label="__('Base Unit')" required>
                    <option value="">{{ __('Select unit') }}</option>
                    @foreach ($unitOptions as $unit)
                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                    @endforeach
                </flux:select>
            </div>

            <div class="grid gap-4 md:grid-cols-3">
                <flux:input wire:model.defer="editForm.min_stock" :label="__('Minimum Stock')" type="number"
                    step="0.01" min="0" />

                <flux:select wire:model.defer="editForm.track_stock" :label="__('Track Stock')" required>
                    <option value="1">{{ __('Yes') }}</option>
                    <option value="0">{{ __('No') }}</option>
                </flux:select>

                <flux:select wire:model.defer="editForm.has_expiry" :label="__('Has Expiry')" required>
                    <option value="1">{{ __('Yes') }}</option>
                    <option value="0">{{ __('No') }}</option>
                </flux:select>
            </div>

            <flux:select wire:model.defer="editForm.is_active" :label="__('Status')" required>
                <option value="1">{{ __('Active') }}</option>
                <option value="0">{{ __('Inactive') }}</option>
            </flux:select>

            <div class="space-y-4 rounded-xl border border-[#142a28]/70 bg-[#10211f]/40 p-4">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div class="space-y-1">
                        <flux:heading size="lg">{{ __('Additional Units') }}</flux:heading>
                        <flux:text variant="subtle">
                            {{ __('Base unit is saved automatically as 1 unit. Conversion qty can be left blank to use 1, or filled manually, for example 1 Box = 12 Pcs.') }}
                        </flux:text>
                    </div>

                    @if ($editForm['units_id'] !== '')
                        <flux:button type="button" variant="ghost" wire:click="addProductUnitRow('editForm')">
                            {{ __('Add Conversion Unit') }}
                        </flux:button>
                    @endif
                </div>

                @php
                    $editBaseUnit = $unitOptions->firstWhere('id', (int) ($editForm['units_id'] ?? 0));
                @endphp

                @if ($editBaseUnit)
                    <div class="rounded-lg border border-[#142a28]/60 bg-[#1c3432]/70 px-4 py-3 text-sm text-[#a9c2bd]">
                        {{ __('Base unit will be saved as 1 :unit. Leave conversion qty empty to use 1, or fill a custom value.', ['unit' => $editBaseUnit->name]) }}
                    </div>
                @else
                    <div class="rounded-lg border border-[#142a28]/60 bg-[#1c3432]/70 px-4 py-3 text-sm text-[#a9c2bd]">
                        {{ __('Select a base unit first before adding unit conversions.') }}
                    </div>
                @endif

                @foreach ($editForm['product_units'] as $index => $productUnit)
                    @php
                        $selectedUnit = $unitOptions->firstWhere('id', (int) ($productUnit['unit_id'] ?? 0));
                    @endphp

                    <div wire:key="edit-product-unit-{{ $index }}"
                        class="space-y-3 rounded-lg border border-[#142a28]/60 bg-[#1c3432]/50 p-4">
                        <div class="grid gap-4 md:grid-cols-[1.5fr_1fr_1fr_auto]">
                            <flux:select wire:model.defer="editForm.product_units.{{ $index }}.unit_id"
                                :label="__('Additional Unit')">
                                <option value="">{{ __('Select unit') }}</option>
                                @foreach ($unitOptions as $unit)
                                    @if ((string) $unit->id !== (string) ($editForm['units_id'] ?? ''))
                                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                    @endif
                                @endforeach
                            </flux:select>

                            <flux:input wire:model.defer="editForm.product_units.{{ $index }}.conversion_qty"
                                :label="__('Qty for Selected Unit')" type="text" inputmode="decimal"
                                placeholder="{{ __('Leave blank for 1, or example: 12') }}" />

                            <flux:select wire:model.defer="editForm.product_units.{{ $index }}.is_active"
                                :label="__('Status')" required>
                                <option value="1">{{ __('Active') }}</option>
                                <option value="0">{{ __('Inactive') }}</option>
                            </flux:select>

                            <div class="flex items-end">
                                <flux:button type="button" variant="danger" wire:click="removeProductUnitRow('editForm', {{ $index }})">
                                    {{ __('Remove') }}
                                </flux:button>
                            </div>
                        </div>

                        @if ($editBaseUnit && $selectedUnit && ($productUnit['conversion_qty'] ?? '') !== '')
                            <p class="text-sm text-[#a9c2bd]">
                                {{ __('1 :base = :qty :unit', ['base' => $editBaseUnit->name, 'qty' => $productUnit['conversion_qty'], 'unit' => $selectedUnit->name]) }}
                            </p>
                        @endif
                    </div>
                @endforeach
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
