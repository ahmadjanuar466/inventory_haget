<x-page-body>
    <x-breadcumbs :datas="$breadcumbs"></x-breadcumbs>

    <x-page-title :title="$pageTitle['title']" :subtitle="$pageTitle['subtitle']"></x-page-title>

    <x-table-section>
        <x-table-navbar>
            <x-table-navbar-title title="{{ __('List Stock') }}"
                subtitle="{{ __('View current stock by product and warehouse.') }}">
            </x-table-navbar-title>

            <x-table-navbar-search-button>
                <x-table-navbar-per-page-option modellive="perPage" :pageOptions="$perPageOptions">
                </x-table-navbar-per-page-option>

                <flux:select wire:model.live="filters.warehouse_id" class="sm:w-56">
                    <option value="">{{ __('All Warehouses') }}</option>
                    @foreach ($warehouseOptions as $warehouse)
                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                    @endforeach
                </flux:select>

                <flux:input wire:model.live.debounce.400ms="search" :placeholder="__('Search stock...')"
                    icon="magnifying-glass" class="sm:w-64" />
            </x-table-navbar-search-button>
        </x-table-navbar>

        @php
            $dtHead = ['Product', 'Warehouse', 'On Hand', 'Reserved', 'Available', 'Last Movement', 'Actions'];
        @endphp

        <x-table-custom>
            <x-table-head :head="$dtHead"></x-table-head>

            <x-table-body :items="$stocks" row-view="livewire.inventory.stock.partials.stock-row" :columns="7"
                item-key="stock" empty-message="{{ __('No stock data found.') }}" />
        </x-table-custom>

        <div class="pt-2">
            {{ $stocks->links() }}
        </div>
    </x-table-section>

    <x-modal name="stock-card-modal" class="max-w-5xl" title="{{ __('Stock Card') }}"
        subtitle="{{ __('Product stock movements for the selected warehouse.') }}"
        wire:model="showStockCardModal" wire-target="showStockCard" :createFeedback="''"
        close-action="closeStockCardModal" loading-message="{{ __('Loading stock card...') }}">
        @if ($selectedStock)
            <div class="grid gap-3 md:grid-cols-4">
                <div class="rounded-lg border border-[#142a28]/70 bg-[#10211f]/50 p-4">
                    <div class="text-xs uppercase text-[#a9c2bd]">{{ __('Product') }}</div>
                    <div class="mt-1 font-semibold">{{ $selectedStock->product->name ?? __('Unknown Product') }}</div>
                    <div class="text-xs text-[#a9c2bd]">{{ $selectedStock->product->sku ?? '-' }}</div>
                </div>

                <div class="rounded-lg border border-[#142a28]/70 bg-[#10211f]/50 p-4">
                    <div class="text-xs uppercase text-[#a9c2bd]">{{ __('Warehouse') }}</div>
                    <div class="mt-1 font-semibold">{{ $selectedStock->warehouse->name ?? __('Unknown Warehouse') }}</div>
                    <div class="text-xs text-[#a9c2bd]">{{ $selectedStock->warehouse->code ?? '-' }}</div>
                </div>

                <div class="rounded-lg border border-[#142a28]/70 bg-[#10211f]/50 p-4">
                    <div class="text-xs uppercase text-[#a9c2bd]">{{ __('Available') }}</div>
                    <div class="mt-1 text-xl font-semibold">{{ number_format((float) $selectedStock->qty_available, 2) }}</div>
                    <div class="text-xs text-[#a9c2bd]">{{ $selectedStock->product->units->name ?? __('Unit') }}</div>
                </div>

                <div class="rounded-lg border border-[#142a28]/70 bg-[#10211f]/50 p-4">
                    <div class="text-xs uppercase text-[#a9c2bd]">{{ __('On Hand') }}</div>
                    <div class="mt-1 text-xl font-semibold">{{ number_format((float) $selectedStock->qty_on_hand, 2) }}</div>
                    <div class="text-xs text-[#a9c2bd]">
                        {{ __('Reserved: :qty', ['qty' => number_format((float) $selectedStock->qty_reserved, 2)]) }}
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-[#142a28]/60 text-left text-sm">
                    <thead>
                        <tr class="text-xs uppercase tracking-wide text-[#a9c2bd]">
                            <th class="px-4 py-3">{{ __('Date') }}</th>
                            <th class="px-4 py-3">{{ __('Type') }}</th>
                            <th class="px-4 py-3 text-right">{{ __('Qty In') }}</th>
                            <th class="px-4 py-3 text-right">{{ __('Qty Out') }}</th>
                            <th class="px-4 py-3 text-right">{{ __('Balance') }}</th>
                            <th class="px-4 py-3">{{ __('Reference') }}</th>
                            <th class="px-4 py-3">{{ __('Notes') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#142a28]/60 text-[#f4f1ec]">
                        @forelse ($stockMovements as $movement)
                            <tr class="transition hover:bg-[#142a28]/60">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    {{ $movement->movement_date?->format('d M Y H:i') ?? '-' }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="rounded-full border border-[#d6c172]/30 bg-[#d6c172]/10 px-2 py-1 text-xs text-[#f4e8a4]">
                                        {{ $movement->movement_type }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">{{ number_format((float) $movement->qty_in, 2) }}</td>
                                <td class="px-4 py-3 text-right">{{ number_format((float) $movement->qty_out, 2) }}</td>
                                <td class="px-4 py-3 text-right">{{ number_format((float) $movement->qty_balance_after, 2) }}</td>
                                <td class="px-4 py-3 text-[#a9c2bd]">
                                    {{ class_basename($movement->reference_type) }} #{{ $movement->reference_id }}
                                </td>
                                <td class="px-4 py-3 text-[#a9c2bd]">{{ $movement->notes ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-6 text-center text-[#a9c2bd]">
                                    {{ __('No stock movements found for this product and warehouse.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    </x-modal>
</x-page-body>
