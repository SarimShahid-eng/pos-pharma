@extends('layouts.master')

@section('title', isset($sale) ? 'Edit Sale' : 'Create Sale')

@section('content')
    <div class="max-w-6xl mx-auto space-y-6" x-data="saleForm()">


        <!-- Header Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-muted">Point of Sale</span>
                <h1 class="font-display text-2xl font-bold text-forest">
                    {{ isset($sale) ? 'Edit Sale: #' . @$sale->invoice_number : 'New Sales Invoice' }}
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

                <a href="{{ route('sales.index') }}"
                    class="inline-flex items-center gap-2 rounded-[var(--radius-s)] border border-line bg-white px-3.5 py-2 text-xs font-semibold text-ink hover:bg-paper transition-colors">
                    <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Sales
                </a>
            </div>
        </div>

        <form action="{{ route('sales.store') }}" method="POST" @submit="preventEmptySubmit($event)" class="space-y-6">
            @csrf
            <input type="hidden" name="update_id" value="{{ @$sale->id }}">

            <!-- Invoice & General Info Header -->
            <div class="panel bg-white space-y-4">
                <h2 class="text-xs font-bold uppercase tracking-wider text-forest border-b border-line pb-2">
                    1. Invoice Setup
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Invoice Number (Auto Generated / Readonly) -->
                    <div>
                        <label for="invoice_number"
                            class="block text-xs font-semibold uppercase tracking-wider text-muted mb-1">
                            Invoice # <span class="text-tag-red">*</span>
                        </label>
                        <input type="text" name="invoice_number" id="invoice_number"
                            value="{{ old('invoice_number', $sale->invoice_number ?? $generatedInvoiceNumber) }}" readonly
                            class="w-full rounded-[var(--radius-s)] border border-line bg-forest-tint/50 px-3.5 py-2 text-sm font-mono font-bold text-forest cursor-not-allowed">
                        @error('invoice_number')
                            <p class="mt-1 text-xs text-tag-red font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Sale Date -->
                    <div>
                        <label for="date" class="block text-xs font-semibold uppercase tracking-wider text-muted mb-1">
                            Sale Date <span class="text-tag-red">*</span>
                        </label>
                        <input type="date" name="date" id="date"
                            value="{{ old('date', isset($sale) ? $sale->date->format('Y-m-d') : date('Y-m-d')) }}" required
                            class="w-full rounded-[var(--radius-s)] border @error('date') border-tag-red @else border-line @enderror bg-paper px-3.5 py-2 text-sm text-ink focus:border-forest focus:bg-white focus:outline-none focus:ring-1 focus:ring-forest transition-colors">
                        @error('date')
                            <p class="mt-1 text-xs text-tag-red font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Payment Method -->
                    <div>
                        <label for="payment_method"
                            class="block text-xs font-semibold uppercase tracking-wider text-muted mb-1">
                            Payment Method <span class="text-tag-red">*</span>
                        </label>
                        <select name="payment_method" id="payment_method" required
                            class="w-full rounded-[var(--radius-s)] border @error('payment_method') border-tag-red @else border-line @enderror bg-paper px-3.5 py-2 text-sm text-ink focus:border-forest focus:bg-white focus:outline-none focus:ring-1 focus:ring-forest transition-colors">
                            <option value="cash"
                                {{ old('payment_method', $sale->payment_method ?? 'cash') === 'cash' ? 'selected' : '' }}>
                                Cash
                            </option>
                            <option value="card"
                                {{ old('payment_method', $sale->payment_method ?? '') === 'card' ? 'selected' : '' }}>
                                Card
                            </option>
                            <option value="bank"
                                {{ old('payment_method', $sale->payment_method ?? '') === 'bank' ? 'selected' : '' }}>
                                Online / Digital
                            </option>
                        </select>
                        @error('payment_method')
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
                        <h2 class="text-xs font-bold uppercase tracking-wider text-forest">2. Sale Items</h2>
                        <p class="text-[11px] text-muted">Scan barcode or select items below to build customer invoice.</p>
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
                                <th class="py-3 px-4 min-w-[240px]">Product</th>
                                <th class="py-3 px-4 w-28">Qty</th>
                                <th class="py-3 px-4 w-36">Unit Rate (Rs)</th>
                                <th class="py-3 px-4 w-32">Discount (Rs)</th>
                                <th class="py-3 px-4 w-36">After Disc Price</th>
                                <th class="py-3 px-4 w-40">Line Subtotal</th>
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

                                    <!-- Quantity -->
                                    <td class="py-2.5 px-4">
                                        <input type="number" step="1" min="1" :name="`items[${index}][qty]`"
                                            x-model.number="item.qty" required
                                            class="w-full rounded-[var(--radius-s)] border border-line bg-paper px-3 py-1.5 text-xs font-mono text-ink focus:border-forest focus:bg-white focus:outline-none">
                                    </td>

                                    <!-- Unit Rate -->
                                    <td class="py-2.5 px-4">
                                        <input type="number" step="0.01" min="0"
                                            :name="`items[${index}][rate]`" x-model.number="item.rate" required
                                            class="w-full rounded-[var(--radius-s)] border border-line bg-paper px-3 py-1.5 text-xs font-mono text-ink focus:border-forest focus:bg-white focus:outline-none">
                                    </td>

                                    <!-- Per-unit Discount -->
                                    <td class="py-2.5 px-4">
                                        <input type="number" step="0.01" min="0"
                                            :name="`items[${index}][discount]`" x-model.number="item.discount"
                                            placeholder="0.00"
                                            class="w-full rounded-[var(--radius-s)] border border-line bg-paper px-3 py-1.5 text-xs font-mono text-ink focus:border-forest focus:bg-white focus:outline-none">
                                    </td>

                                    <!-- After Discount Price Display -->
                                    <td class="py-2.5 px-4 font-mono text-xs font-medium text-ink">
                                        <input type="hidden" :name="`items[${index}][after_discount_price]`"
                                            :value="calculateAfterDiscountPrice(item)">
                                        Rs <span x-text="calculateAfterDiscountPrice(item)"></span>
                                    </td>

                                    <!-- Line Amount (Qty * After Discount Price) -->
                                    <td class="py-2.5 px-4 font-mono text-xs font-semibold text-forest">
                                        <input type="hidden" :name="`items[${index}][amount]`"
                                            :value="calculateItemAmount(item)">
                                        Rs <span x-text="calculateItemAmount(item)"></span>
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

                <!-- Add Row Button -->
                <div class="p-4 bg-paper/50 border-t border-line flex items-center justify-between">
                    <button type="button" @click="addItem()"
                        class="inline-flex items-center gap-1.5 rounded-[var(--radius-s)] border border-line bg-white px-3 py-1.5 text-xs font-semibold text-ink hover:bg-paper transition-colors">
                        + Add Item Row
                    </button>
                </div>
            </div>

            <!-- Financial Calculations Section -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Additional Info -->
                <div class="panel bg-white space-y-2 lg:col-span-1">
                    <span class="block text-xs font-semibold uppercase tracking-wider text-muted">
                        Walk-in Customer Sale
                    </span>
                    <p class="text-xs text-muted leading-relaxed">
                        This checkout is configured for walk-in retail counter sales. Stock quantities will automatically
                        decrement upon transaction completion.
                    </p>
                </div>

                <!-- Payment Calculations Card -->
                <div class="panel bg-white space-y-3 lg:col-span-2">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-forest border-b border-line pb-2">
                        3. Checkout Summary
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Total Amount (Gross) -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-muted mb-1">
                                Gross Total Amount
                            </label>
                            <input type="hidden" name="total_amount" :value="calculateTotalAmount()">
                            <div
                                class="w-full rounded-[var(--radius-s)] border border-line bg-paper px-3 py-2 text-sm font-mono font-bold text-ink">
                                Rs <span x-text="calculateTotalAmount().toFixed(2)"></span>
                            </div>
                        </div>

                        <!-- Total Discounts -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-muted mb-1">
                                Total Discount (Rs)
                            </label>
                            <input type="hidden" name="discount_amount" :value="calculateTotalDiscounts()">
                            <div
                                class="w-full rounded-[var(--radius-s)] border border-line bg-forest-tint/40 px-3 py-2 text-sm font-mono font-bold text-forest cursor-not-allowed">
                                Rs <span x-text="calculateTotalDiscounts().toFixed(2)"></span>
                            </div>
                        </div>

                        <!-- Net Amount Payable -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-muted mb-1">
                                Net Amount
                            </label>
                            <input type="hidden" name="net_amount" :value="calculateNetAmount()">
                            <div
                                class="w-full rounded-[var(--radius-s)] border border-forest bg-forest-tint/30 px-3 py-2 text-base font-mono font-bold text-forest">
                                Rs <span x-text="calculateNetAmount().toFixed(2)"></span>
                            </div>
                        </div>

                        <!-- Received Amount Input -->
                        {{-- <div>
                            <label for="received_amount"
                                class="block text-xs font-semibold uppercase tracking-wider text-muted mb-1">
                                Received Amount (Rs)
                            </label>
                            <input type="number" step="0.01" min="0" name="received_amount"
                                id="received_amount" x-model.number="receivedAmount"
                                class="w-full rounded-[var(--radius-s)] border @error('received_amount') border-tag-red @else border-line @enderror bg-paper px-3 py-2 text-sm font-mono text-ink focus:border-forest focus:bg-white focus:outline-none">
                            @error('received_amount')
                                <p class="mt-1 text-xs text-tag-red font-medium">{{ $message }}</p>
                            @enderror
                        </div> --}}
                    </div>

                    <!-- Change Due Display -->
                    {{-- <div class="mt-3 pt-3 border-t border-line flex items-center justify-between">
                        <span class="text-xs font-semibold uppercase tracking-wider text-muted">
                            Change to Return:
                        </span>
                        <input type="hidden" name="change_given" :value="calculateChangeGiven()">
                        <span class="font-mono text-lg font-bold"
                            :class="calculateChangeGiven() < 0 ? 'text-tag-red' : 'text-forest'"
                            x-text="'Rs ' + calculateChangeGiven().toFixed(2)"></span>
                    </div> --}}
                </div>
            </div>

            <!-- Form Action Controls -->
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('sales.index') }}"
                    class="rounded-[var(--radius-s)] border border-line bg-white px-4 py-2.5 text-xs font-semibold text-muted hover:bg-paper transition-colors">
                    Cancel
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-[var(--radius-s)] bg-forest px-6 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-forest-light transition-all cursor-pointer">
                    <span>{{ isset($sale) ? 'Update Sale' : 'Complete Sale' }}</span>
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('saleForm', () => ({
                scannedBarcode: '',
                receivedAmount: {{ old('received_amount', $sale->received_amount ?? 0) }},
                productsList: @json($products),
                items: @json(old('items', $saleItems ?? [])),

                init() {
                    if (this.items.length === 0) {
                        this.addItem();
                    }

                    // Keep focus on barcode scanner unless actively editing inputs
                    this.$nextTick(() => this.focusScanner());
                    window.addEventListener('click', (e) => {
                        if (!['INPUT', 'SELECT', 'TEXTAREA', 'BUTTON'].includes(e.target
                                .tagName)) {
                            this.focusScanner();
                        }
                    });
                },

                focusScanner() {
                    if (this.$refs.barcodeScanner) {
                        this.$refs.barcodeScanner.focus();
                    }
                },

                addItem(productId = '', qty = 1, rate = 0, discount = 0) {
                    this.items.push({
                        product_id: productId,
                        qty: qty,
                        rate: rate,
                        discount: discount
                    });
                },

                removeItem(index) {
                    this.items.splice(index, 1);
                    if (this.items.length === 0) {
                        this.addItem();
                    }
                },

                // Unit after discount price: Rate - Discount
                calculateAfterDiscountPrice(item) {
                    const qty = parseFloat(item.qty) || 1;
                    const lineSubtotal = parseFloat(this.calculateItemAmount(item)) || 0;

                    return (lineSubtotal / qty).toFixed(2);
                },

                // Line subtotal amount: Qty * After Discount Price
                calculateItemAmount(item) {
                    const qty = parseFloat(item.qty) || 0;
                    const rate = parseFloat(item.rate) || 0;
                    const lineDiscount = parseFloat(item.discount) || 0;

                    const grossSubtotal = qty * rate;
                    const lineAmount = grossSubtotal - lineDiscount;

                    return (lineAmount > 0 ? lineAmount : 0).toFixed(2);
                },
                // Gross total before discount across all items: Qty * Rate
                calculateTotalAmount() {
                    return this.items.reduce((sum, item) => {
                        return sum + ((parseFloat(item.qty) || 0) * (parseFloat(item.rate) ||
                            0));
                    }, 0);
                },

                // Combined total discount across all items: Qty * Discount
                calculateTotalDiscounts() {
                    return this.items.reduce((sum, item) => {
                        return sum + (parseFloat(item.discount) || 0);
                    }, 0);
                },
                // Net payable amount
                calculateNetAmount() {
                    const net = this.calculateTotalAmount() - this.calculateTotalDiscounts();
                    return net > 0 ? net : 0;
                },

                // Change given to walk-in customer: Received - Net
                calculateChangeGiven() {
                    const received = parseFloat(this.receivedAmount) || 0;
                    const change = received - this.calculateNetAmount();
                    return change > 0 ? change : 0;
                },

                onProductSelect(index) {
                    let selectedId = this.items[index].product_id;
                    let product = this.productsList.find(p => p.id == selectedId);
                    if (product) {
                        // Prefill default retail price (or fallback cost)
                        this.items[index].rate = parseFloat(product.sale_price ||
                            product.cost_price) || 0;
                    }
                },

                scanBarcode() {
                    let code = this.scannedBarcode.trim();
                    if (!code) return;

                    let product = this.productsList.find(p => p.barcode == code);

                    if (product) {
                        let existing = this.items.find(i => i.product_id == product.id);
                        let defaultRate = parseFloat(product.sale_price || product
                            .cost_price) || 0;

                        if (existing) {
                            existing.qty += 1;
                        } else {
                            if (this.items.length === 1 && !this.items[0].product_id) {
                                this.items[0] = {
                                    product_id: product.id,
                                    qty: 1,
                                    rate: defaultRate,
                                    discount: 0
                                };
                            } else {
                                this.addItem(product.id, 1, defaultRate, 0);
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
                        alert('Please add at least one product item before completing the sale.');
                    }
                }
            }));
        });
    </script>
@endsection
