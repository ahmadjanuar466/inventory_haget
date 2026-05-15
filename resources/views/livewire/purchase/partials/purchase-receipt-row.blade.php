<tr class="border-b border-[#142a28]/60">
    <td class="px-4 py-3">
        <div class="font-semibold">{{ $purchaseReceipt->receipt_no }}</div>
        <div class="text-xs text-[#a9c2bd]">{{ $purchaseReceipt->invoice_no ?: '-' }}</div>
    </td>
    <td class="px-4 py-3">{{ $purchaseReceipt->supplier->name ?? '-' }}</td>
    <td class="px-4 py-3">{{ $purchaseReceipt->warehouse->name ?? '-' }}</td>
    <td class="px-4 py-3">{{ $purchaseReceipt->receipt_date?->format('d M Y') ?? '-' }}</td>
    <td class="px-4 py-3 font-semibold text-[#d6c172]">
        {{ number_format((float) $purchaseReceipt->total_amount, 2) }}
    </td>
    <td class="px-4 py-3">
        <span class="rounded-full border border-[#d6c172]/40 px-3 py-1 text-xs capitalize text-[#d6c172]">
            {{ $purchaseReceipt->status }}
        </span>
    </td>
    <td class="px-4 py-3">
        <button type="button" wire:click="showItems({{ $purchaseReceipt->id }})"
            class="text-sm font-semibold text-[#d6c172] hover:underline">
            {{ __('Items') }}
        </button>
    </td>
    <td class="px-4 py-3">
        @if ($purchaseReceipt->invoice_file)
            <button type="button" wire:click="showInvoice({{ $purchaseReceipt->id }})"
                class="text-sm font-semibold text-[#d6c172] hover:underline">
                {{ __('View') }}
            </button>
        @else
            <span class="text-sm text-[#a9c2bd]">-</span>
        @endif
    </td>
    <td class="px-4 py-3">
        <div class="flex justify-end">
            <flux:button type="button" size="sm" variant="danger"
                wire:click="confirmDelete({{ $purchaseReceipt->id }})" wire:loading.attr="disabled"
                wire:target="confirmDelete">
                {{ __('Delete') }}
            </flux:button>
        </div>
    </td>
</tr>
