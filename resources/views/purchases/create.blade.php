@extends('layouts.master')

@section('title', isset($purchase) ? 'Edit Purchase' : 'Create Purchase')

@section('content')
    <div class="max-w-6xl mx-auto space-y-6" x-data="purchaseForm()">

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Header Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-muted">Stock Management</span>
                <h1 class="font-display text-2xl font-bold text-forest">
                    {{ isset($purchase) ? 'Edit Purchase: #' . @$purchase->invoice_number : 'New Purchase Invoice' }}
                </h1>
            </div>
            <div class="flex items-center gap-2.5">
                <!-- Safety Scanner Re-focus Button -->
                <button type="button" @click="focusScanner()"
                    class="inline-flex items-center gap-1.5 rounded-[var(--radius-s)] border border-line bg-paper px-3 py-2 text-xs font-semibold text-forest hover:bg-forest-tint transition-colors cursor-pointer">
                    <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Re-activate Scanner</span>
                </button>

                <a href="{{ route('purchases.index') }}"
                    class="inline-flex items-center gap-2 rounded-[var(--radius-s)] border border-line bg-white px-3.5 py-2 text-xs font-semibold text-ink hover:bg-paper transition-colors">
                    <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Purchases
                </a>
            </div>
        </div>

        <form action="{{ route('purchases.store') }}" method="POST" @submit="preventEmptySubmit($event)" class="space-y-6">
            @csrf
            <input type="hidden" name="update_id" value="{{ @$purchase->id }}">

            <!-- Invoice & Supplier Header -->
            <div class="panel bg-white space-y-4">
                <h2 class="text-xs font-bold uppercase tracking-wider text-forest border-b border-line pb-2">
                    1. Invoice & Supplier Setup
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <!-- Invoice Number (Auto Generated / Readonly) -->
                    <div>
                        <label for="invoice_number"
                            class="block text-xs font-semibold uppercase tracking-wider text-muted mb-1">
                            Invoice # <span class="text-tag-red">*</span>
                        </label>
                        <input type="text" name="invoice_number" id="invoice_number"
                            value="{{ old('invoice_number', $purchase->invoice_number ?? $generatedInvoiceNumber) }}"
                            readonly
                            class="w-full rounded-[var(--radius-s)] border border-line bg-forest-tint/50 px-3.5 py-2 text-sm font-mono font-bold text-forest cursor-not-allowed">
                        @error('invoice_number')
                            <p class="mt-1 text-xs text-tag-red font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Supplier Reference Number (Manual Entry) -->
                    <div>
                        <label for="reference_number"
                            class="block text-xs font-semibold uppercase tracking-wider text-muted mb-1">
                            Reference #
                        </label>
                        <input type="text" name="reference_number" id="reference_number"
                            value="{{ old('reference_number', $purchase->reference_number ?? '') }}"
                            placeholder="e.g. SUP-INV-0012"
                            class="w-full rounded-[var(--radius-s)] border @error('reference_number') border-tag-red @else border-line @enderror bg-paper px-3.5 py-2 text-sm font-mono text-ink focus:border-forest focus:bg-white focus:outline-none focus:ring-1 focus:ring-forest transition-colors">
                        @error('reference_number')
                            <p class="mt-1 text-xs text-tag-red font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Supplier Selection -->
                    <div class="space-y-1">
                        <label class="block text-xs font-semibold text-ink">Supplier <span
                                class="text-tag-red">*</span></label>
                        <select name="supplier_id" x-model="supplierId" x-select2="supplierId" required
                            placeholder="Search supplier..." class="w-full">
                            <option value="">Select Supplier</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}"
                                    {{ old('supplier_id', $purchase->supplier_id ?? '') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('supplier_id')
                            <p class="mt-1 text-xs text-tag-red font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Purchase Date -->
                    <div>
                        <label for="date" class="block text-xs font-semibold uppercase tracking-wider text-muted mb-1">
                            Purchase Date <span class="text-tag-red">*</span>
                        </label>
                        <input type="date" name="date" id="date"
                            value="{{ old('date', isset($purchase) ? $purchase->date->format('Y-m-d') : date('Y-m-d')) }}"
                            required
                            class="w-full rounded-[var(--radius-s)] border @error('date') border-tag-red @else border-line @enderror bg-paper px-3.5 py-2 text-sm text-ink focus:border-forest focus:bg-white focus:outline-none focus:ring-1 focus:ring-forest transition-colors">
                        @error('date')
                            <p class="mt-1 text-xs text-tag-red font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-xs font-semibold uppercase tracking-wider text-muted mb-1">
                            Status <span class="text-tag-red">*</span>
                        </label>
                        <select name="status" id="status" required
                            class="w-full rounded-[var(--radius-s)] border @error('status') border-tag-red @else border-line @enderror bg-paper px-3.5 py-2 text-sm text-ink focus:border-forest focus:bg-white focus:outline-none focus:ring-1 focus:ring-forest transition-colors">
                            <option value="received"
                                {{ old('status', $purchase->status ?? 'received') === 'received' ? 'selected' : '' }}>
                                Received</option>
                            <option value="pending"
                                {{ old('status', $purchase->status ?? '') === 'pending' ? 'selected' : '' }}>Pending
                            </option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-xs text-tag-red font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Barcode Scanner & Line Items Panel -->
            <div class="panel bg-white space-y-4 p-0 overflow-hidden">
                <div
                    class="p-4 bg-paper border-b border-line flex flex-col md:flex-row md:items-center justify-between gap-3">
                    <div>
                        <h2 class="text-xs font-bold uppercase tracking-wider text-forest">2. Purchase Items</h2>
                        <p class="text-[11px] text-muted">Scan barcode or select items below to populate invoice.</p>
                    </div>

                    <!-- Always Active Scan Input -->
                    <div class="relative min-w-[280px]">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-forest">
                            <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                        </div>
                        <input type="text" x-ref="barcodeScanner" x-model="scannedBarcode"
                            @keydown.enter.prevent="scanBarcode()" placeholder="Scan Barcode Here..."
                            class="w-full rounded-[var(--radius-s)] border border-forest bg-white pl-9 pr-3.5 py-2 text-xs font-mono text-ink placeholder:text-muted focus:outline-none focus:ring-2 focus:ring-forest">
                    </div>
                </div>

                <!-- Items Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-ink">
                        <thead
                            class="bg-paper/50 border-b border-line text-xs font-semibold uppercase tracking-wider text-muted">
                            <tr>
                                <th class="py-3 px-4 min-w-[220px]">Product</th>
                                <th class="py-3 px-4 w-24">Qty</th>
                                <th class="py-3 px-4 w-28">Bonus Qty</th>
                                <th class="py-3 px-4 w-40">Cost Price (Rs)</th>
                                <th class="py-3 px-4 w-32">Discount (Rs)</th>
                                <th class="py-3 px-4 w-40">Subtotal (Rs)</th>
                                <th class="py-3 px-4 w-16 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line">
                            <template x-for="(item, index) in items" :key="index">
                                <tr class="hover:bg-paper/30 transition-colors">
                                    <!-- Product Selection -->
                                    <td class="py-2.5 px-4">
                                        <select :name="`items[${index}][product_id]`" x-model="item.product_id"
                                            x-select2="item.product_id" @change="onProductSelect(index)"
                                            placeholder="Type or scan product name..." class="w-full text-xs">
                                            <option value="">Select Product</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}">
                                                    {{ $product->name }} ({{ $product->barcode ?? 'N/A' }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>

                                    <!-- Quantity (paid) -->
                                    <td class="py-2.5 px-4">
                                        <input type="number" step="1" min="1"
                                            :name="`items[${index}][qty]`" x-model.number="item.qty" required
                                            class="w-full rounded-[var(--radius-s)] border border-line bg-paper px-3 py-1.5 text-xs font-mono text-ink focus:border-forest focus:bg-white focus:outline-none">
                                    </td>

                                    <!-- Bonus / Free Quantity (not billed, doesn't affect cost) -->
                                    <td class="py-2.5 px-4">
                                        <input type="number" step="1" min="0"
                                            :name="`items[${index}][bonus_qty]`" x-model.number="item.bonus_qty"
                                            placeholder="0"
                                            class="w-full rounded-[var(--radius-s)] border border-line bg-paper px-3 py-1.5 text-xs font-mono text-ink focus:border-forest focus:bg-white focus:outline-none">
                                    </td>

                                    <!-- Unit Cost -->
                                    <td class="py-2.5 px-4">
                                        <input type="number" step="0.01" min="0"
                                            :name="`items[${index}][unit_cost]`" x-model.number="item.unit_cost" required
                                            class="w-full rounded-[var(--radius-s)] border border-line bg-paper px-3 py-1.5 text-xs font-mono text-ink focus:border-forest focus:bg-white focus:outline-none">
                                    </td>

                                    <!-- Line Item Discount -->
                                    <td class="py-2.5 px-4">
                                        <input type="number" step="0.01" min="0"
                                            :name="`items[${index}][discount_amount]`"
                                            x-model.number="item.discount_amount" placeholder="0.00"
                                            class="w-full rounded-[var(--radius-s)] border border-line bg-paper px-3 py-1.5 text-xs font-mono text-ink focus:border-forest focus:bg-white focus:outline-none">
                                    </td>

                                    <!-- Subtotal Display (Qty * Cost - Item Discount, bonus qty excluded) -->
                                    <td class="py-2.5 px-4 font-mono text-xs font-semibold text-forest">
                                        <input type="hidden" :name="`items[${index}][subtotal_amount]`"
                                            :value="calculateItemSubtotal(item)">
                                        Rs <span x-text="calculateItemSubtotal(item)"></span>
                                    </td>

                                    <!-- Delete Row -->
                                    <td class="py-2.5 px-4 text-center">
                                        <button type="button" @click="removeItem(index)"
                                            class="text-tag-red hover:bg-tag-red/10 p-1 rounded transition-colors"
                                            title="Remove Item">
                                            <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Add Row Button + Bonus Qty Summary -->
                <div
                    class="p-4 bg-paper/50 border-t border-line flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <button type="button" @click="addItem()"
                        class="inline-flex items-center gap-1.5 rounded-[var(--radius-s)] border border-line bg-white px-3 py-1.5 text-xs font-semibold text-ink hover:bg-paper transition-colors">
                        + Add Item Row
                    </button>

                    <div class="text-xs font-semibold text-muted">
                        Total Bonus Qty (free stock):
                        <input type="hidden" name="total_bonus_qty" :value="calculateTotalBonusQty()">
                        <span class="font-mono font-bold text-forest" x-text="calculateTotalBonusQty()"></span>
                        units
                    </div>
                </div>
            </div>

            <!-- Financial Calculations & Notes Section -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Purchase Notes -->
                <div class="panel bg-white space-y-2 lg:col-span-1">
                    <label for="notes" class="block text-xs font-semibold uppercase tracking-wider text-muted">
                        Notes / Remarks
                    </label>
                    <textarea name="notes" id="notes" rows="4" placeholder="Add optional purchase notes..."
                        class="w-full rounded-[var(--radius-s)] border border-line bg-paper p-3 text-xs text-ink focus:border-forest focus:bg-white focus:outline-none focus:ring-1 focus:ring-forest transition-colors">{{ old('notes', $purchase->notes ?? '') }}</textarea>
                </div>

                <!-- Payment Calculations Card -->
                <div class="panel bg-white space-y-3 lg:col-span-2">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-forest border-b border-line pb-2">
                        3. Payment Details
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Items Subtotal (Gross, before discounts) -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-muted mb-1">
                                Subtotal Amount
                            </label>
                            <div
                                class="w-full rounded-[var(--radius-s)] border border-line bg-paper px-3 py-2 text-sm font-mono font-bold text-ink">
                                Rs <span x-text="calculateSubtotal().toFixed(2)"></span>
                            </div>
                        </div>

                        <!-- Discount Amount (auto-summed from item discounts, disabled) -->
                        <div>
                            <label for="discount_amount"
                                class="block text-xs font-semibold uppercase tracking-wider text-muted mb-1">
                                Discount Amount (Rs)
                            </label>
                            <input type="hidden" name="discount_amount" :value="calculateTotalItemDiscounts()">
                            <div
                                class="w-full rounded-[var(--radius-s)] border border-line bg-forest-tint/40 px-3 py-2 text-sm font-mono font-bold text-forest cursor-not-allowed">
                                Rs <span x-text="calculateTotalItemDiscounts().toFixed(2)"></span>
                            </div>
                        </div>

                        <!-- Payable Total Amount (Subtotal - Discounts) -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-muted mb-1">
                                Net Total Amount
                            </label>
                            <input type="hidden" name="total_amount" :value="calculateNetTotal()">
                            <div
                                class="w-full rounded-[var(--radius-s)] border border-forest bg-forest-tint/30 px-3 py-2 text-base font-mono font-bold text-forest">
                                Rs <span x-text="calculateNetTotal().toFixed(2)"></span>
                            </div>
                        </div>

                        <!-- Paid Amount Input -->
                        <div>
                            <label for="paid_amount"
                                class="block text-xs font-semibold uppercase tracking-wider text-muted mb-1">
                                Paid Amount (Rs)
                            </label>
                            <input type="number" step="0.01" min="0" name="paid_amount" id="paid_amount"
                                x-model.number="paidAmount"
                                class="w-full rounded-[var(--radius-s)] border @error('paid_amount') border-tag-red @else border-line @enderror bg-paper px-3 py-2 text-sm font-mono text-ink focus:border-forest focus:bg-white focus:outline-none">
                            @error('paid_amount')
                                <p class="mt-1 text-xs text-tag-red font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Due Balance Summary Display -->
                    <div class="mt-3 pt-3 border-t border-line flex items-center justify-between">
                        <span class="text-xs font-semibold uppercase tracking-wider text-muted">Remaining Balance
                            (Due):</span>
                        <input type="hidden" name="remaining_amount" :value="calculateRemaining()">
                        <span class="font-mono text-lg font-bold"
                            :class="calculateRemaining() > 0 ? 'text-tag-red' : 'text-forest'"
                            x-text="'Rs ' + calculateRemaining().toFixed(2)"></span>
                    </div>
                </div>
            </div>

            <!-- Form Action Controls -->
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('purchases.index') }}"
                    class="rounded-[var(--radius-s)] border border-line bg-white px-4 py-2.5 text-xs font-semibold text-muted hover:bg-paper transition-colors">
                    Cancel
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-[var(--radius-s)] bg-forest px-6 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-forest-light transition-all cursor-pointer">
                    <span>{{ isset($purchase) ? 'Update Purchase' : 'Save Purchase' }}</span>
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('purchaseForm', () => ({
                supplierId: @json(old('supplier_id', $purchase->supplier_id ?? '')),
                scannedBarcode: '',
                paidAmount: {{ old('paid_amount', $purchase->supplierPayment->amount ?? 0) }},
                productsList: @json($products),
                items: @json(old('items', $purchaseItems ?? [])),

                init() {
                    if (this.items.length === 0) {
                        this.addItem();
                    }

                    // Always keep focus on barcode scanner unless editing a specific input
                    this.$nextTick(() => this.focusScanner());
                    window.addEventListener('click', (e) => {
                        if (!['INPUT', 'SELECT', 'TEXTAREA', 'BUTTON'].includes(e.target
                                .tagName)) {
                            this.focusScanner();
                        }
                    });
                },

                focusScanner() {
                    this.$refs.barcodeScanner.focus();
                },

                addItem(productId = '', qty = 1, unitCost = 0, discountAmount = 0, bonusQty = 0) {
                    this.items.push({
                        product_id: productId,
                        qty: qty,
                        bonus_qty: bonusQty,
                        unit_cost: unitCost,
                        discount_amount: discountAmount
                    });
                },

                removeItem(index) {
                    this.items.splice(index, 1);
                    if (this.items.length === 0) {
                        this.addItem();
                    }
                },

                // Per-row subtotal: paid qty * cost - item discount (bonus qty is free, excluded from cost)
                calculateItemSubtotal(item) {
                    const gross = (parseFloat(item.qty) || 0) * (parseFloat(item.unit_cost) || 0);
                    const discount = parseFloat(item.discount_amount) || 0;
                    return (gross - discount).toFixed(2);
                },

                // Sum of (qty * cost) across all rows, before discounts
                calculateSubtotal() {
                    return this.items.reduce((sum, item) => {
                        return sum + ((parseFloat(item.qty) || 0) * (parseFloat(item
                            .unit_cost) || 0));
                    }, 0);
                },

                // Sum of per-item discounts
                calculateTotalItemDiscounts() {
                    return this.items.reduce((sum, item) => {
                        return sum + (parseFloat(item.discount_amount) || 0);
                    }, 0);
                },

                // Sum of free/bonus units received across all rows
                calculateTotalBonusQty() {
                    return this.items.reduce((sum, item) => {
                        return sum + (parseInt(item.bonus_qty) || 0);
                    }, 0);
                },

                // Subtotal minus total item discounts
                calculateNetTotal() {
                    const total = this.calculateSubtotal() - this.calculateTotalItemDiscounts();
                    return total > 0 ? total : 0;
                },

                calculateRemaining() {
                    const due = this.calculateNetTotal() - (parseFloat(this.paidAmount) || 0);
                    return due > 0 ? due : 0;
                },

                onProductSelect(index) {
                    let selectedId = this.items[index].product_id;
                    let product = this.productsList.find(p => p.id == selectedId);
                    if (product) {
                        this.items[index].unit_cost = parseFloat(product.cost_price) || 0;
                    }
                },

                scanBarcode() {
                    let code = this.scannedBarcode.trim();
                    if (!code) return;

                    let product = this.productsList.find(p => p.barcode == code);

                    if (product) {
                        let existing = this.items.find(i => i.product_id == product.id);
                        if (existing) {
                            existing.qty += 1;
                        } else {
                            // Check if first row is empty, replace it; else push new
                            if (this.items.length === 1 && !this.items[0].product_id) {
                                this.items[0] = {
                                    product_id: product.id,
                                    qty: 1,
                                    bonus_qty: 0,
                                    unit_cost: parseFloat(product.cost_price) || 0,
                                    discount_amount: 0
                                };
                            } else {
                                this.addItem(product.id, 1, parseFloat(product.cost_price) || 0);
                            }
                        }
                    } else {
                        alert('No product found with barcode: ' + code);
                    }

                    this.scannedBarcode = '';
                    this.focusScanner();
                },

                preventEmptySubmit(event) {
                    if (this.items.length === 0 || !this.items.some(i => i.product_id)) {
                        event.preventDefault();
                        alert('Please add at least one product item before saving.');
                    }
                }
            }));
        });
    </script>
@endsection
