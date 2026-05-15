<x-page-body>
    <x-breadcumbs :datas="$breadcumbs"></x-breadcumbs>

    @if ($feedback !== '')
        <div class="rounded-lg border border-[#d6c172]/40 bg-[#10211f]/60 px-4 py-3 text-sm text-[#d6c172]">
            {{ $feedback }}
        </div>
    @endif

    <x-page-title :title="$pageTitle['title']" :subtitle="$pageTitle['subtitle']"></x-page-title>

    <x-table-section>
        <form wire:submit.prevent="savePurchaseReceipt" class="space-y-5">
            <div class="grid gap-4 md:grid-cols-4">
                <flux:input wire:model.defer="form.receipt_no" :label="__('Receipt No')" type="text" required />
                <flux:input wire:model.defer="form.receipt_date" :label="__('Receipt Date')" type="date" required />

                <flux:select wire:model.defer="form.supplier_id" :label="__('Supplier')" required>
                    <option value="">{{ __('Select supplier') }}</option>
                    @foreach ($supplierOptions as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                    @endforeach
                </flux:select>

                <flux:select wire:model.defer="form.warehouse_id" :label="__('Warehouse')" required>
                    <option value="">{{ __('Select warehouse') }}</option>
                    @foreach ($warehouseOptions as $warehouse)
                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                    @endforeach
                </flux:select>
            </div>

            <div class="grid gap-4 md:grid-cols-3">
                <flux:input wire:model.defer="form.invoice_no" :label="__('Invoice No')" type="text" />

                <flux:select wire:model.defer="form.status" :label="__('Status')" required>
                    <option value="approved">{{ __('Approved') }}</option>
                    <option value="draft">{{ __('Draft') }}</option>
                </flux:select>

                <div>
                    <label class="text-sm font-medium text-[#f4f1ec]">{{ __('Invoice File') }}</label>
                    <input wire:model="invoiceFile" type="file" accept=".pdf,.jpg,.jpeg,.png"
                        class="mt-2 block w-full rounded-lg border border-[#142a28] bg-[#10211f]/70 px-3 py-2 text-sm text-[#f4f1ec] file:mr-3 file:rounded-md file:border-0 file:bg-[#d6c172] file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-[#142a28] focus:border-[#d6c172] focus:outline-none" />
                    @error('invoiceFile')
                        <p class="mt-2 text-sm text-red-300">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <flux:textarea wire:model.defer="form.notes" :label="__('Notes')" rows="2" />

            <div class="space-y-4 rounded-lg border border-[#142a28]/70 bg-[#10211f]/40 p-4">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <flux:heading size="lg">{{ __('Purchase Receipt Items') }}</flux:heading>

                    <flux:button type="button" variant="ghost" wire:click="addItemRow">
                        {{ __('Add Item') }}
                    </flux:button>
                </div>

                <div class="space-y-3">
                    <div
                        class="hidden grid-cols-[minmax(220px,1.4fr)_minmax(90px,.55fr)_minmax(120px,.75fr)_minmax(110px,.7fr)_minmax(110px,.7fr)_minmax(110px,.7fr)_minmax(110px,.7fr)_minmax(120px,.8fr)_auto] gap-3 px-3 text-xs font-semibold uppercase text-[#a9c2bd] xl:grid">
                        <div>{{ __('Product') }}</div>
                        <div>{{ __('Qty') }}</div>
                        <div>{{ __('Purchase Unit') }}</div>
                        <div>{{ __('Stock Qty') }}</div>
                        <div>{{ __('Cost') }}</div>
                        <div>{{ __('Discount') }}</div>
                        <div>{{ __('Tax') }}</div>
                        <div class="text-right">{{ __('Subtotal') }}</div>
                        <div></div>
                    </div>

                    @foreach ($form['items'] as $index => $item)
                        @php
                            $selectedProduct = $productOptions->firstWhere('id', (int) ($item['product_id'] ?? 0));
                            $availableUnitIds = $selectedProduct
                                ? [(int) $selectedProduct->units_id]
                                : [];

                            $availableUnitOptions = $selectedProduct
                                ? $unitOptions->whereIn('id', $availableUnitIds)
                                : collect();
                        @endphp

                        <div wire:key="purchase-item-{{ $index }}"
                            class="rounded-lg border border-[#142a28]/70 bg-[#1c3432]/60 p-3">
                            <div
                                class="grid gap-3 md:grid-cols-2 xl:grid-cols-[minmax(220px,1.4fr)_minmax(90px,.55fr)_minmax(120px,.75fr)_minmax(110px,.7fr)_minmax(110px,.7fr)_minmax(110px,.7fr)_minmax(110px,.7fr)_minmax(120px,.8fr)_auto] xl:items-start">
                                <div class="space-y-1">
                                    <div class="text-xs font-semibold uppercase text-[#a9c2bd] xl:hidden">
                                        {{ __('Product') }}
                                    </div>
                                    <flux:select wire:model.live="form.items.{{ $index }}.product_id"
                                        aria-label="{{ __('Product') }}" required>
                                        <option value="">{{ __('Select product') }}</option>
                                        @foreach ($productOptions as $product)
                                            <option value="{{ $product->id }}">
                                                {{ $product->name }}{{ $product->sku ? ' - ' . $product->sku : '' }}
                                            </option>
                                        @endforeach
                                    </flux:select>
                                </div>

                                <div class="space-y-1">
                                    <div class="text-xs font-semibold uppercase text-[#a9c2bd] xl:hidden">
                                        {{ __('Qty') }}
                                    </div>
                                    <flux:input wire:model.live.debounce.300ms="form.items.{{ $index }}.qty"
                                        aria-label="{{ __('Qty') }}" type="number" step="0.01" min="0.01" required />
                                </div>

                                <div class="space-y-1">
                                    <div class="text-xs font-semibold uppercase text-[#a9c2bd] xl:hidden">
                                        {{ __('Purchase Unit') }}
                                    </div>
                                    <flux:select wire:model.live="form.items.{{ $index }}.unit_id"
                                        aria-label="{{ __('Purchase Unit') }}" required>
                                        <option value="">{{ __('Select purchase unit') }}</option>
                                        @foreach ($availableUnitOptions as $unit)
                                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                        @endforeach
                                    </flux:select>
                                </div>

                                <div class="space-y-1">
                                    <div class="text-xs font-semibold uppercase text-[#a9c2bd] xl:hidden">
                                        {{ __('Stock Qty') }}
                                    </div>
                                    <flux:input wire:key="purchase-item-conversion-{{ $index }}-{{ $item['conversion_qty'] ?? 'empty' }}"
                                        wire:model="form.items.{{ $index }}.conversion_qty"
                                        aria-label="{{ __('Stock Qty') }}" type="number" step="0.01" min="0.01"
                                        readonly />
                                </div>

                                <div class="space-y-1">
                                    <div class="text-xs font-semibold uppercase text-[#a9c2bd] xl:hidden">
                                        {{ __('Cost') }}
                                    </div>
                                    <flux:input wire:model.live.debounce.300ms="form.items.{{ $index }}.cost_price"
                                        aria-label="{{ __('Cost') }}" type="number" step="0.01" min="0" required />
                                </div>

                                <div class="space-y-1">
                                    <div class="text-xs font-semibold uppercase text-[#a9c2bd] xl:hidden">
                                        {{ __('Discount') }}
                                    </div>
                                    <flux:input wire:model.live.debounce.300ms="form.items.{{ $index }}.discount_amount"
                                        aria-label="{{ __('Discount') }}" type="number" step="0.01" min="0" />
                                </div>

                                <div class="space-y-1">
                                    <div class="text-xs font-semibold uppercase text-[#a9c2bd] xl:hidden">
                                        {{ __('Tax') }}
                                    </div>
                                    <flux:input wire:model.live.debounce.300ms="form.items.{{ $index }}.tax_amount"
                                        aria-label="{{ __('Tax') }}" type="number" step="0.01" min="0" />
                                </div>

                                <div class="space-y-1 rounded-md border border-[#142a28]/60 bg-[#10211f]/50 px-3 py-2 text-right xl:border-0 xl:bg-transparent xl:px-0">
                                    <div class="text-xs font-semibold uppercase text-[#a9c2bd] xl:hidden">
                                        {{ __('Subtotal') }}
                                    </div>
                                    <div class="text-sm font-semibold text-[#d6c172]">
                                        {{ number_format((float) ($item['subtotal'] ?? 0), 2) }}
                                    </div>
                                </div>

                                <div class="flex items-end md:col-span-2 xl:col-span-1">
                                    <flux:button type="button" variant="danger"
                                        wire:click="removeItemRow({{ $index }})">
                                        {{ __('Remove') }}
                                    </flux:button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-4">
                <div class="rounded-lg border border-[#142a28]/70 bg-[#10211f]/40 p-4">
                    <div class="text-xs uppercase text-[#a9c2bd]">{{ __('Subtotal') }}</div>
                    <div class="mt-1 text-lg font-semibold">{{ number_format((float) $form['subtotal'], 2) }}</div>
                </div>

                <div class="rounded-lg border border-[#142a28]/70 bg-[#10211f]/40 p-4">
                    <div class="text-xs uppercase text-[#a9c2bd]">{{ __('Discount') }}</div>
                    <div class="mt-1 text-lg font-semibold">{{ number_format((float) $form['discount_amount'], 2) }}</div>
                </div>

                <div class="rounded-lg border border-[#142a28]/70 bg-[#10211f]/40 p-4">
                    <div class="text-xs uppercase text-[#a9c2bd]">{{ __('Tax') }}</div>
                    <div class="mt-1 text-lg font-semibold">{{ number_format((float) $form['tax_amount'], 2) }}</div>
                </div>

                <div class="rounded-lg border border-[#d6c172]/50 bg-[#10211f]/60 p-4">
                    <div class="text-xs uppercase text-[#a9c2bd]">{{ __('Total') }}</div>
                    <div class="mt-1 text-lg font-semibold text-[#d6c172]">{{ number_format((float) $form['total_amount'], 2) }}</div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <flux:button type="button" variant="ghost" wire:click="resetForm" wire:loading.attr="disabled"
                    wire:target="savePurchaseReceipt">
                    {{ __('Reset') }}
                </flux:button>

                <flux:button type="submit" variant="primary" wire:loading.attr="disabled"
                    wire:target="savePurchaseReceipt,invoiceFile">
                    <span wire:loading.remove wire:target="savePurchaseReceipt">
                        {{ __('Save Purchase Receipt') }}
                    </span>
                    <span wire:loading wire:target="savePurchaseReceipt" class="inline-flex items-center gap-2">
                        <flux:icon.loading class="h-4 w-4" />
                        {{ __('Saving...') }}
                    </span>
                </flux:button>
            </div>
        </form>
    </x-table-section>

    <x-table-section>
        <x-table-navbar>
            <x-table-navbar-title title="{{ __('List Purchase Receipt') }}"
                subtitle="{{ __('Review incoming purchase receipts and uploaded invoices.') }}">
            </x-table-navbar-title>

            <x-table-navbar-search-button>
                <x-table-navbar-per-page-option modellive="perPage" :pageOptions="$perPageOptions">
                </x-table-navbar-per-page-option>

                <flux:input wire:model.live.debounce.400ms="search" :placeholder="__('Search receipts...')"
                    icon="magnifying-glass" class="sm:w-64" />
            </x-table-navbar-search-button>
        </x-table-navbar>

        @php
            $dtHead = ['Receipt', 'Supplier', 'Warehouse', 'Date', 'Total', 'Status', 'Items', 'Invoice', 'Action'];
        @endphp

        <x-table-custom>
            <x-table-head :head="$dtHead"></x-table-head>
            <x-table-body :items="$purchaseReceipts" row-view="livewire.purchase.partials.purchase-receipt-row"
                :columns="9" item-key="purchaseReceipt" empty-message="{{ __('No purchase receipts found.') }}" />
        </x-table-custom>

        <div class="pt-2">
            {{ $purchaseReceipts->links() }}
        </div>
    </x-table-section>

    <flux:modal name="delete-purchase-receipt-modal" wire:model="showDeleteModal" focusable class="max-w-md"
        @close="$wire.cancelDelete()">
        <div class="relative space-y-6">
            <div wire:loading wire:target="deletePurchaseReceipt"
                class="absolute inset-0 z-20 flex items-center justify-center rounded-xl bg-[#0d1a18]/70">
                <span class="inline-flex items-center gap-2 text-sm font-semibold text-[#d6c172]">
                    <flux:icon.loading class="h-5 w-5" />
                    {{ __('Deleting purchase receipt...') }}
                </span>
            </div>

            <div class="flex items-start justify-between gap-4">
                <div class="space-y-1">
                    <flux:heading size="lg">{{ __('Delete Purchase Receipt') }}</flux:heading>
                    <flux:text variant="subtle">
                        {{ __('This action will also reduce the related stock.') }}
                    </flux:text>
                </div>

                <flux:button variant="ghost" size="sm" icon="x-mark" wire:click="cancelDelete">
                    <span class="sr-only">{{ __('Close') }}</span>
                </flux:button>
            </div>

            <p class="text-sm text-[#a9c2bd]">
                {{ __('Are you sure you want to delete purchase receipt ":receipt"?', ['receipt' => $deleteContextName]) }}
            </p>

            @error('delete')
                <p class="text-sm text-red-300">{{ $message }}</p>
            @enderror

            <div class="flex items-center justify-end gap-3">
                <flux:button type="button" variant="ghost" wire:click="cancelDelete" wire:loading.attr="disabled"
                    wire:target="deletePurchaseReceipt">
                    {{ __('Cancel') }}
                </flux:button>

                <flux:button type="button" variant="danger" wire:click="deletePurchaseReceipt"
                    wire:loading.attr="disabled" wire:target="deletePurchaseReceipt">
                    <span wire:loading.remove wire:target="deletePurchaseReceipt">
                        {{ __('Delete') }}
                    </span>
                    <span wire:loading wire:target="deletePurchaseReceipt" class="inline-flex items-center gap-2">
                        <flux:icon.loading class="h-4 w-4" />
                        {{ __('Deleting...') }}
                    </span>
                </flux:button>
            </div>
        </div>
    </flux:modal>

    <x-modal name="purchase-items-modal" class="max-w-6xl" title="{{ __('Purchase Items') }}"
        subtitle="{{ $selectedItemsReceipt ? __('Receipt :receipt', ['receipt' => $selectedItemsReceipt->receipt_no]) : __('Items in the selected purchase receipt.') }}"
        wire:model="showItemsModal" wire-target="showItems" :createFeedback="''" close-action="closeItemsModal"
        loading-message="{{ __('Loading items...') }}">
        @if ($selectedItemsReceipt)
            <div class="space-y-4">
                <div class="grid gap-3 md:grid-cols-4">
                    <div class="rounded-lg border border-[#142a28]/70 bg-[#10211f]/50 p-4">
                        <div class="text-xs uppercase text-[#a9c2bd]">{{ __('Supplier') }}</div>
                        <div class="mt-1 font-semibold">{{ $selectedItemsReceipt->supplier->name ?? '-' }}</div>
                    </div>

                    <div class="rounded-lg border border-[#142a28]/70 bg-[#10211f]/50 p-4">
                        <div class="text-xs uppercase text-[#a9c2bd]">{{ __('Warehouse') }}</div>
                        <div class="mt-1 font-semibold">{{ $selectedItemsReceipt->warehouse->name ?? '-' }}</div>
                    </div>

                    <div class="rounded-lg border border-[#142a28]/70 bg-[#10211f]/50 p-4">
                        <div class="text-xs uppercase text-[#a9c2bd]">{{ __('Date') }}</div>
                        <div class="mt-1 font-semibold">{{ $selectedItemsReceipt->receipt_date?->format('d M Y') ?? '-' }}</div>
                    </div>

                    <div class="rounded-lg border border-[#d6c172]/40 bg-[#10211f]/60 p-4">
                        <div class="text-xs uppercase text-[#a9c2bd]">{{ __('Total') }}</div>
                        <div class="mt-1 font-semibold text-[#d6c172]">
                            {{ number_format((float) $selectedItemsReceipt->total_amount, 2) }}
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-[#142a28]/60 text-left text-sm">
                        <thead class="bg-[#142a28]/80 text-xs uppercase tracking-wide text-[#a9c2bd]">
                            <tr>
                                <th class="px-4 py-3">{{ __('Product') }}</th>
                                <th class="px-4 py-3 text-right">{{ __('Purchase Qty') }}</th>
                                <th class="px-4 py-3 text-right">{{ __('Stock Qty') }}</th>
                                <th class="px-4 py-3 text-right">{{ __('Cost') }}</th>
                                <th class="px-4 py-3 text-right">{{ __('Discount') }}</th>
                                <th class="px-4 py-3 text-right">{{ __('Tax') }}</th>
                                <th class="px-4 py-3 text-right">{{ __('Subtotal') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#142a28]/60 text-[#f4f1ec]">
                            @forelse ($selectedItemsReceipt->items as $item)
                                @php
                                    $stockUnit = $item->product?->productUnits
                                        ?->first(fn ($productUnit) => (int) $productUnit->is_active === 1)
                                        ?->unit;
                                @endphp

                                <tr class="transition hover:bg-[#142a28]/50">
                                    <td class="px-4 py-3">
                                        <div class="font-semibold">{{ $item->product->name ?? __('Unknown Product') }}</div>
                                        <div class="text-xs text-[#a9c2bd]">{{ $item->product->sku ?? '-' }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-right tabular-nums">
                                        <div class="font-semibold">{{ number_format((float) $item->qty, 2) }}</div>
                                        <div class="text-xs text-[#a9c2bd]">{{ $item->unit->name ?? __('Unit') }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-right tabular-nums">
                                        <div class="font-semibold text-[#f4e8a4]">{{ number_format((float) $item->conversion_qty, 2) }}</div>
                                        <div class="text-xs text-[#a9c2bd]">{{ $stockUnit->name ?? __('Unit') }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-right tabular-nums">{{ number_format((float) $item->cost_price, 2) }}</td>
                                    <td class="px-4 py-3 text-right tabular-nums">{{ number_format((float) $item->discount_amount, 2) }}</td>
                                    <td class="px-4 py-3 text-right tabular-nums">{{ number_format((float) $item->tax_amount, 2) }}</td>
                                    <td class="px-4 py-3 text-right font-semibold tabular-nums text-[#d6c172]">
                                        {{ number_format((float) $item->subtotal, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-6 text-center text-[#a9c2bd]">
                                        {{ __('No purchase items found.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </x-modal>

    <x-modal name="invoice-preview-modal" class="max-w-5xl" title="{{ __('Invoice') }}"
        subtitle="{{ $selectedInvoiceReceiptNo ? __('Receipt :receipt', ['receipt' => $selectedInvoiceReceiptNo]) : __('Preview uploaded invoice file.') }}"
        wire:model="showInvoiceModal" wire-target="showInvoice" :createFeedback="''" close-action="closeInvoiceModal"
        loading-message="{{ __('Loading invoice...') }}">
        @if ($selectedInvoiceUrl)
            <div class="space-y-4">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div class="min-w-0">
                        <div class="text-xs font-semibold uppercase text-[#a9c2bd]">{{ __('File') }}</div>
                        <div class="truncate text-sm font-semibold text-[#f4f1ec]">
                            {{ $selectedInvoiceName }}
                        </div>
                    </div>

                    <flux:button href="{{ $selectedInvoiceUrl }}" target="_blank" variant="ghost" icon="arrow-top-right-on-square">
                        {{ __('Open') }}
                    </flux:button>
                </div>

                @if ($selectedInvoiceType === 'pdf')
                    <iframe src="{{ $selectedInvoiceUrl }}"
                        class="h-[70vh] w-full rounded-lg border border-[#142a28]/70 bg-[#10211f]"
                        title="{{ __('Invoice preview') }}"></iframe>
                @elseif ($selectedInvoiceType === 'image')
                    <div class="flex max-h-[70vh] items-center justify-center overflow-auto rounded-lg border border-[#142a28]/70 bg-[#10211f] p-3">
                        <img src="{{ $selectedInvoiceUrl }}" alt="{{ __('Invoice preview') }}"
                            class="max-h-[68vh] max-w-full rounded-md object-contain" />
                    </div>
                @else
                    <div class="rounded-lg border border-[#142a28]/70 bg-[#10211f]/50 px-4 py-6 text-center text-sm text-[#a9c2bd]">
                        {{ __('Preview is not available for this file type. Open the file in a new tab instead.') }}
                    </div>
                @endif
            </div>
        @endif
    </x-modal>
</x-page-body>
