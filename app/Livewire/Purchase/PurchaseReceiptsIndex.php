<?php

namespace App\Livewire\Purchase;

use App\Services\Purchase\PurchaseReceiptServices;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class PurchaseReceiptsIndex extends Component
{
    use WithFileUploads;
    use WithPagination;

    protected PurchaseReceiptServices $purchaseReceiptServices;

    public array $breadcumb = [
        ['title' => 'Dashboard', 'routes' => 'dashboard'],
        ['title' => 'Purchase', 'routes' => ''],
        ['title' => 'Purchase Receipt', 'routes' => ''],
    ];

    public array $pageTitle = [
        'title' => 'Purchase Receipt',
        'subtitle' => 'Record supplier receipts, invoice files, and incoming stock.',
    ];

    public string $search = '';

    public int $perPage = 10;

    public array $perPageOptions = [10, 20, 30, 40, 50];

    public array $form = [];

    public ?TemporaryUploadedFile $invoiceFile = null;

    public string $feedback = '';

    public bool $showInvoiceModal = false;

    public ?string $selectedInvoiceUrl = null;

    public ?string $selectedInvoiceName = null;

    public ?string $selectedInvoiceType = null;

    public ?string $selectedInvoiceReceiptNo = null;

    public bool $showItemsModal = false;

    public ?int $selectedItemsReceiptId = null;

    public bool $showDeleteModal = false;

    public ?int $deletingPurchaseReceiptId = null;

    public ?string $deleteContextName = null;

    protected $paginationTheme = 'tailwind';

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    public function boot(PurchaseReceiptServices $purchaseReceiptServices): void
    {
        $this->purchaseReceiptServices = $purchaseReceiptServices;
    }

    public function mount(): void
    {
        $this->resetForm();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage($value): void
    {
        $this->perPage = in_array((int) $value, $this->perPageOptions, true) ? (int) $value : 10;
        $this->resetPage();
    }

    public function updatedForm($value, string $key): void
    {
        if (! str_starts_with($key, 'items.')) {
            return;
        }

        $this->handleItemUpdate((string) str($key)->after('items.'));
    }

    public function updatedFormItems($value, string $key): void
    {
        $this->handleItemUpdate($key);
    }

    protected function handleItemUpdate(string $key): void
    {
        $parts = explode('.', $key);
        $index = (int) ($parts[0] ?? 0);
        $field = $parts[1] ?? '';

        if (isset($this->form['items'][$index])) {
            if ($field === 'product_id') {
                $this->syncItemUnit($index);
            }

            if (in_array($field, ['product_id', 'unit_id', 'qty'], true)) {
                $this->syncItemConversionQty($index);
            }

            $this->recalculateItem($index);
            $this->recalculateTotals();
        }
    }

    public function addItemRow(): void
    {
        $this->form['items'][] = $this->defaultItem();
    }

    public function removeItemRow(int $index): void
    {
        if (count($this->form['items']) <= 1) {
            $this->form['items'] = [$this->defaultItem()];
            return;
        }

        unset($this->form['items'][$index]);
        $this->form['items'] = array_values($this->form['items']);
        $this->recalculateTotals();
    }

    public function savePurchaseReceipt(): void
    {
        $this->feedback = '';
        $this->prepareFormForValidation();

        $validated = $this->validate($this->rules(), attributes: $this->validationAttributes());

        $invoicePath = $this->invoiceFile?->store('purchase-invoices', 'public');

        $purchaseData = [
            ...$validated['form'],
            'invoice_file' => $invoicePath,
            'created_by' => auth()->id(),
        ];

        unset($purchaseData['items']);

        $itemData = collect($validated['form']['items'])
            ->map(fn (array $item): array => [
                'product_id' => (int) $item['product_id'],
                'qty' => (float) $item['qty'],
                'unit_id' => (int) $item['unit_id'],
                'conversion_qty' => $this->normalizeDecimal($item['conversion_qty'] ?? '') ?? (float) $item['qty'],
                'cost_price' => (float) $item['cost_price'],
                'discount_amount' => $this->normalizeDecimal($item['discount_amount'] ?? '') ?? 0,
                'tax_amount' => $this->normalizeDecimal($item['tax_amount'] ?? '') ?? 0,
                'subtotal' => $this->lineSubtotal($item),
            ])
            ->all();

        $this->purchaseReceiptServices->createPurchaseReceipt($purchaseData, $itemData);

        $this->resetForm();
        $this->resetPage();
        $this->feedback = __('Purchase receipt saved and stock updated.');
    }

    public function resetForm(): void
    {
        $this->invoiceFile = null;
        $this->form = [
            'receipt_no' => $this->generateReceiptNo(),
            'receipt_date' => now()->toDateString(),
            'supplier_id' => '',
            'warehouse_id' => '',
            'invoice_no' => '',
            'status' => 'approved',
            'subtotal' => 0,
            'discount_amount' => 0,
            'tax_amount' => 0,
            'total_amount' => 0,
            'notes' => '',
            'items' => [$this->defaultItem()],
        ];

        $this->resetValidation();
    }

    public function showInvoice(int $purchaseReceiptId): void
    {
        $purchaseReceipt = $this->purchaseReceiptServices->getPurchaseReceiptById($purchaseReceiptId);

        if (! $purchaseReceipt->invoice_file) {
            return;
        }

        $this->selectedInvoiceUrl = Storage::disk('public')->url($purchaseReceipt->invoice_file);
        $this->selectedInvoiceName = basename($purchaseReceipt->invoice_file);
        $this->selectedInvoiceType = $this->resolveInvoiceType($purchaseReceipt->invoice_file);
        $this->selectedInvoiceReceiptNo = $purchaseReceipt->receipt_no;
        $this->showInvoiceModal = true;
    }

    public function closeInvoiceModal(): void
    {
        $this->showInvoiceModal = false;
        $this->selectedInvoiceUrl = null;
        $this->selectedInvoiceName = null;
        $this->selectedInvoiceType = null;
        $this->selectedInvoiceReceiptNo = null;
    }

    public function showItems(int $purchaseReceiptId): void
    {
        $this->selectedItemsReceiptId = $purchaseReceiptId;
        $this->showItemsModal = true;
    }

    public function closeItemsModal(): void
    {
        $this->showItemsModal = false;
        $this->selectedItemsReceiptId = null;
    }

    public function confirmDelete(int $purchaseReceiptId): void
    {
        $purchaseReceipt = $this->purchaseReceiptServices->getPurchaseReceiptById($purchaseReceiptId);

        $this->deletingPurchaseReceiptId = $purchaseReceiptId;
        $this->deleteContextName = $purchaseReceipt->receipt_no;
        $this->resetErrorBag(['delete']);
        $this->showDeleteModal = true;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deletingPurchaseReceiptId = null;
        $this->deleteContextName = null;
        $this->resetErrorBag(['delete']);
    }

    public function deletePurchaseReceipt(): void
    {
        if (! $this->deletingPurchaseReceiptId) {
            return;
        }

        $purchaseReceipt = $this->purchaseReceiptServices->getPurchaseReceiptById($this->deletingPurchaseReceiptId);
        $receiptNo = $this->deleteContextName ?? $purchaseReceipt->receipt_no;
        $invoiceFile = $purchaseReceipt->invoice_file;

        try {
            $this->purchaseReceiptServices->deletePurchaseReceipt($purchaseReceipt);
        } catch (ValidationException $exception) {
            $this->addError('delete', collect($exception->errors())->flatten()->first() ?: __('Purchase receipt cannot be deleted because stock is insufficient.'));

            return;
        }

        if ($invoiceFile) {
            Storage::disk('public')->delete($invoiceFile);
        }

        if ($this->selectedItemsReceiptId === $this->deletingPurchaseReceiptId) {
            $this->closeItemsModal();
        }

        $this->showDeleteModal = false;
        $this->deletingPurchaseReceiptId = null;
        $this->deleteContextName = null;
        $this->resetPage();
        $this->feedback = __('Purchase receipt :receipt deleted and stock updated.', ['receipt' => $receiptNo]);
    }

    public function render(): View
    {
        return view('livewire.purchase.purchase-receipts-index', [
            'purchaseReceipts' => $this->purchaseReceiptServices->paginatePurchaseReceipts($this->search, $this->perPage),
            'selectedItemsReceipt' => $this->purchaseReceiptServices->getSelectedItemsReceipt($this->selectedItemsReceiptId),
            'supplierOptions' => $this->purchaseReceiptServices->getSupplierOptions(),
            'warehouseOptions' => $this->purchaseReceiptServices->getWarehouseOptions(),
            'productOptions' => $this->purchaseReceiptServices->getProductOptions(),
            'unitOptions' => $this->purchaseReceiptServices->getUnitOptions(),
            'perPageOptions' => $this->perPageOptions,
            'breadcumbs' => $this->breadcumb,
        ])->layout('components.layouts.app', [
            'title' => __('Purchase Receipt'),
        ]);
    }

    protected function rules(): array
    {
        return [
            'form.receipt_no' => ['required', 'string', 'max:50', Rule::unique('purchase_receipts', 'receipt_no')],
            'form.receipt_date' => ['required', 'date'],
            'form.supplier_id' => ['required', Rule::exists('suppliers', 'id')],
            'form.warehouse_id' => ['required', Rule::exists('warehouses', 'id')],
            'form.invoice_no' => ['nullable', 'string', 'max:50'],
            'form.status' => ['required', Rule::in(['draft', 'approved'])],
            'form.subtotal' => ['required', 'numeric', 'min:0'],
            'form.discount_amount' => ['required', 'numeric', 'min:0'],
            'form.tax_amount' => ['required', 'numeric', 'min:0'],
            'form.total_amount' => ['required', 'numeric', 'min:0'],
            'form.notes' => ['nullable', 'string'],
            'form.items' => ['required', 'array', 'min:1'],
            'form.items.*.product_id' => ['required', Rule::exists('products', 'id')],
            'form.items.*.qty' => ['required', 'numeric', 'gt:0'],
            'form.items.*.unit_id' => ['required', Rule::exists('units', 'id')],
            'form.items.*.conversion_qty' => ['required', 'numeric', 'gt:0'],
            'form.items.*.cost_price' => ['required', 'numeric', 'min:0'],
            'form.items.*.discount_amount' => ['nullable', 'numeric', 'min:0'],
            'form.items.*.tax_amount' => ['nullable', 'numeric', 'min:0'],
            'invoiceFile' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'form.receipt_no' => __('Receipt No'),
            'form.receipt_date' => __('Receipt Date'),
            'form.supplier_id' => __('Supplier'),
            'form.warehouse_id' => __('Warehouse'),
            'form.invoice_no' => __('Invoice No'),
            'form.items.*.product_id' => __('Product'),
            'form.items.*.qty' => __('Qty'),
            'form.items.*.unit_id' => __('Purchase Unit'),
            'form.items.*.conversion_qty' => __('Conversion Qty'),
            'form.items.*.cost_price' => __('Cost Price'),
            'invoiceFile' => __('Invoice File'),
        ];
    }

    protected function prepareFormForValidation(): void
    {
        foreach ($this->form['items'] as $index => $item) {
            $this->syncItemConversionQty($index);
            $this->recalculateItem($index);
        }

        $this->recalculateTotals();
    }

    protected function resolveInvoiceType(string $invoicePath): string
    {
        $extension = strtolower(pathinfo($invoicePath, PATHINFO_EXTENSION));

        return match ($extension) {
            'pdf' => 'pdf',
            'jpg', 'jpeg', 'png' => 'image',
            default => 'file',
        };
    }

    protected function syncItemUnit(int $index): void
    {
        $productId = (int) ($this->form['items'][$index]['product_id'] ?? 0);

        if ($productId <= 0) {
            return;
        }

        $product = $this->purchaseReceiptServices->getProductById($productId);

        if ($product) {
            $this->form['items'][$index]['unit_id'] = (string) $product->units_id;
        }
    }

    protected function syncItemConversionQty(int $index): void
    {
        if (! isset($this->form['items'][$index])) {
            return;
        }

        $productId = (int) ($this->form['items'][$index]['product_id'] ?? 0);
        $unitId = (int) ($this->form['items'][$index]['unit_id'] ?? 0);
        $qty = $this->normalizeDecimal($this->form['items'][$index]['qty'] ?? '') ?? 0;

        if ($productId <= 0 || $unitId <= 0 || $qty <= 0) {
            $this->form['items'][$index]['conversion_qty'] = '';
            return;
        }

        $productUnit = $this->purchaseReceiptServices->getActiveProductUnit($productId);

        if ($productUnit) {
            $this->form['items'][$index]['conversion_qty'] = $this->formatDecimal($qty * (float) $productUnit->conversion_qty);
            return;
        }

        $product = $this->purchaseReceiptServices->getProductById($productId);
        $conversionQty = $product && (int) $product->units_id === $unitId ? $qty : null;

        $this->form['items'][$index]['conversion_qty'] = $conversionQty === null ? '' : $this->formatDecimal($conversionQty);
    }

    protected function recalculateItem(int $index): void
    {
        if (! isset($this->form['items'][$index])) {
            return;
        }

        $this->form['items'][$index]['subtotal'] = $this->lineSubtotal($this->form['items'][$index]);
    }

    protected function recalculateTotals(): void
    {
        $subtotal = collect($this->form['items'])->sum(fn (array $item): float => $this->lineSubtotal($item));
        $discount = collect($this->form['items'])->sum(fn (array $item): float => $this->normalizeDecimal($item['discount_amount'] ?? '') ?? 0);
        $tax = collect($this->form['items'])->sum(fn (array $item): float => $this->normalizeDecimal($item['tax_amount'] ?? '') ?? 0);

        $this->form['subtotal'] = $subtotal;
        $this->form['discount_amount'] = $discount;
        $this->form['tax_amount'] = $tax;
        $this->form['total_amount'] = $subtotal;
    }

    protected function lineSubtotal(array $item): float
    {
        $qty = $this->normalizeDecimal($item['qty'] ?? '') ?? 0;
        $costPrice = $this->normalizeDecimal($item['cost_price'] ?? '') ?? 0;
        $discount = $this->normalizeDecimal($item['discount_amount'] ?? '') ?? 0;
        $tax = $this->normalizeDecimal($item['tax_amount'] ?? '') ?? 0;

        return max(0, ($qty * $costPrice) - $discount + $tax);
    }

    protected function normalizeDecimal(mixed $value): ?float
    {
        if ($value === '' || $value === null) {
            return null;
        }

        return (float) str_replace(',', '.', (string) $value);
    }

    protected function formatDecimal(float $value): string
    {
        return rtrim(rtrim(number_format($value, 2, '.', ''), '0'), '.');
    }

    protected function defaultItem(): array
    {
        return [
            'product_id' => '',
            'qty' => '',
            'unit_id' => '',
            'conversion_qty' => '',
            'cost_price' => '',
            'discount_amount' => '',
            'tax_amount' => '',
            'subtotal' => 0,
        ];
    }

    protected function generateReceiptNo(): string
    {
        return 'PR-' . now()->format('Ymd-His');
    }
}
