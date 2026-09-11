@extends('layouts.master')

@section('title', 'Create Sale Return')

@section('content')
    <div class="max-w-6xl mx-auto space-y-6" x-data="saleReturnForm()">

        <!-- Header Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-muted">Point of Sale</span>
                <h1 class="font-display text-2xl font-bold text-forest">New Sale Return</h1>
            </div>
            <div class="flex items-center gap-2.5">
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

        <template x-if="submitError">
            <div class="rounded-[var(--radius-s)] border border-tag-red bg-tag-red/5 px-4 py-3 text-xs font-medium text-tag-red"
                x-text="submitError"></div>
        </template>

        <!-- Invoice Search -->
        <div class="panel bg-white space-y-4">
            <h2 class="text-xs font-bold uppercase tracking-wider text-forest border-b border-line pb-2">
                1. Locate Sale Invoice
            </h2>

            <div class="flex flex-col sm:flex-row sm:items-end gap-3">
                <div class="relative flex-1 max-w-md">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-muted mb-1">
                        Invoice Number <span class="text-tag-red">*</span>
                    </label>
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-forest mt-6">
                        <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                    <input type="text" x-ref="invoiceScanner" x-model="invoiceNumber"
                        @keydown.enter.prevent="searchInvoice()" placeholder="Scan or type invoice number..."
                        class="w-full rounded-[var(--radius-s)] border border-forest bg-white pl-9 pr-3.5 py-2 text-sm font-mono text-ink placeholder:text-muted focus:outline-none focus:ring-2 focus:ring-forest">
                </div>
                <button type="button" @click="searchInvoice()" :disabled="searching"
                    class="inline-flex items-center gap-2 rounded-[var(--radius-s)] bg-forest px-5 py-2 text-xs font-semibold text-white shadow-sm hover:bg-forest-light transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                    <span x-text="searching ? 'Searching...' : 'Find Invoice'"></span>
                </button>
            </div>

            <template x-if="searchError">
                <p class="text-xs font-medium text-tag-red" x-text="searchError"></p>
            </template>

            <!-- Loaded Sale Meta -->
            <template x-if="sale">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 pt-3 border-t border-line">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-muted mb-1">Original Invoice #</p>
                        <p class="text-sm font-mono font-bold text-forest" x-text="sale.invoice_number"></p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-muted mb-1">Sale Date</p>
                        <p class="text-sm font-mono" x-text="formatDate(sale.date)"></p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-muted mb-1">Payment Method</p>
                        <p class="text-sm font-mono uppercase" x-text="sale.payment_method"></p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-muted mb-1">Original Net Total</p>
                        <p class="text-sm font-mono font-bold text-ink" x-text="'Rs ' + formatMoney(sale.net_amount)"></p>
                    </div>
                </div>
            </template>
        </div>

        <!-- Return Items -->
        <div class="panel bg-white space-y-0 p-0 overflow-hidden">
            <div class="p-4 bg-paper border-b border-line">
                <h2 class="text-xs font-bold uppercase tracking-wider text-forest">2. Items to Return</h2>
                <p class="text-[11px] text-muted">Set the quantity to return for each product. Leave at 0 to skip an item.
                </p>
            </div>

            <template x-if="!sale">
                <div class="p-8 text-center text-muted text-sm">
                    Search an invoice number above to load its items.
                </div>
            </template>

            <template x-if="sale">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-ink">
                        <thead
                            class="bg-paper/50 border-b border-line text-xs font-semibold uppercase tracking-wider text-muted">
                            <tr>
                                <th class="py-3 px-4 min-w-[200px]">Product</th>
                                <th class="py-3 px-4 w-24 text-center">Sold</th>
                                <th class="py-3 px-4 w-24 text-center">Already Returned</th>
                                <th class="py-3 px-4 w-24 text-center">Available</th>
                                <th class="py-3 px-4 w-28 text-center">Return Qty</th>
                                <th class="py-3 px-4 w-24 text-center">Remaining</th>
                                <th class="py-3 px-4 w-32 text-right">Unit Price</th>
                                <th class="py-3 px-4 w-36 text-right">Line Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line">
                            <template x-for="(item, index) in items" :key="item.sale_item_id">
                                <tr class="hover:bg-paper/30 transition-colors"
                                    :class="item.max_returnable_qty <= 0 ? 'opacity-50' : ''">
                                    <td class="py-2.5 px-4 font-medium" x-text="item.product_name"></td>
                                    <td class="py-2.5 px-4 text-center font-mono" x-text="item.sold_qty"></td>
                                    <td class="py-2.5 px-4 text-center font-mono text-muted"
                                        x-text="item.already_returned_qty"></td>
                                    <td class="py-2.5 px-4 text-center font-mono font-semibold"
                                        x-text="item.max_returnable_qty"></td>
                                    <td class="py-2.5 px-4">
                                        <input type="number" step="1" min="0" :max="item.max_returnable_qty"
                                            x-model.number="item.return_qty" @input="clampReturnQty(index)"
                                            :disabled="item.max_returnable_qty <= 0"
                                            class="w-full rounded-[var(--radius-s)] border border-line bg-paper px-2 py-1.5 text-xs font-mono text-center text-ink focus:border-forest focus:bg-white focus:outline-none disabled:cursor-not-allowed">
                                    </td>
                                    <td class="py-2.5 px-4 text-center font-mono text-muted"
                                        x-text="item.max_returnable_qty - (parseInt(item.return_qty) || 0)"></td>
                                    <td class="py-2.5 px-4 text-right font-mono" x-text="'Rs ' + formatMoney(item.rate)">
                                    </td>
                                    <td class="py-2.5 px-4 text-right font-mono font-semibold text-forest"
                                        x-text="'Rs ' + formatMoney((parseInt(item.return_qty) || 0) * item.rate)"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </template>
        </div>

        <!-- Return Details & Summary -->
        <template x-if="sale">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Return Date & Notes -->
                <div class="panel bg-white space-y-4 lg:col-span-1">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-muted mb-1">
                            Return Date <span class="text-tag-red">*</span>
                        </label>
                        <input type="date" x-model="returnDate"
                            class="w-full rounded-[var(--radius-s)] border border-line bg-paper px-3.5 py-2 text-sm text-ink focus:border-forest focus:bg-white focus:outline-none focus:ring-1 focus:ring-forest transition-colors">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-muted mb-1">
                            Notes / Reason
                        </label>
                        <textarea x-model="notes" rows="4" placeholder="e.g. Wrong item, customer changed mind, damaged product..."
                            class="w-full rounded-[var(--radius-s)] border border-line bg-paper p-3 text-xs text-ink focus:border-forest focus:bg-white focus:outline-none focus:ring-1 focus:ring-forest transition-colors"></textarea>
                    </div>
                </div>

                <!-- Financial Summary -->
                <div class="panel bg-white space-y-3 lg:col-span-2">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-forest border-b border-line pb-2">
                        3. Refund Summary
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-muted mb-1">
                                Total Return Amount
                            </label>
                            <div
                                class="w-full rounded-[var(--radius-s)] border border-forest bg-forest-tint/30 px-3 py-2 text-base font-mono font-bold text-forest">
                                Rs <span x-text="calculateTotalReturnAmount().toFixed(2)"></span>
                            </div>
                            <p class="mt-1 text-[10px] text-muted">Auto-calculated from return quantities x unit price.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-muted mb-1">
                                Refunded Amount (Rs)
                            </label>
                            <input type="number" step="0.01" min="0" x-model.number="refundedAmount"
                                @input="refundedAmountTouched = true"
                                class="w-full rounded-[var(--radius-s)] border border-line bg-paper px-3 py-2 text-sm font-mono text-ink focus:border-forest focus:bg-white focus:outline-none">
                            <p class="mt-1 text-[10px] text-muted">Defaults to the total, but editable — e.g. store credit
                                instead of cash for part of it.</p>
                        </div>
                    </div>

                    <div class="mt-3 pt-3 border-t border-line flex items-center justify-between">
                        <span class="text-xs font-semibold uppercase tracking-wider text-muted">Unsettled
                            Difference:</span>
                        <span class="font-mono text-lg font-bold"
                            :class="(calculateTotalReturnAmount() - (parseFloat(refundedAmount) || 0)) > 0 ? 'text-tag-red' :
                                'text-forest'"
                            x-text="'Rs ' + (calculateTotalReturnAmount() - (parseFloat(refundedAmount) || 0)).toFixed(2)"></span>
                    </div>
                </div>
            </div>
        </template>

        <!-- Form Action Controls -->
        <template x-if="sale">
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('sales.index') }}"
                    class="rounded-[var(--radius-s)] border border-line bg-white px-4 py-2.5 text-xs font-semibold text-muted hover:bg-paper transition-colors">
                    Cancel
                </a>
                <button type="button" @click="submitReturn()" :disabled="submitting"
                    class="inline-flex items-center gap-2 rounded-[var(--radius-s)] bg-tag-red px-6 py-2.5 text-xs font-semibold text-white shadow-sm hover:opacity-90 transition-all cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                    <span x-text="submitting ? 'Processing...' : 'Confirm Return'"></span>
                </button>
            </div>
        </template>

    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('saleReturnForm', () => ({
                invoiceNumber: @json($invoiceNumber ?? ''),
                searching: false,
                searchError: '',

                sale: null,
                items: [],

                returnDate: new Date().toISOString().split('T')[0],
                notes: '',
                totalAmount: 0,
                refundedAmount: 0,
                refundedAmountTouched: false,

                submitting: false,
                submitError: '',

                init() {
                    this.$nextTick(() => this.focusScanner());

                    // Keep refunded amount synced to the calculated total
                    // until the user manually edits it.
                    this.$watch('items', () => {
                        if (!this.refundedAmountTouched) {
                            this.refundedAmount = this.calculateTotalReturnAmount();
                        }
                    }, {
                        deep: true
                    });
                    @if (filled($invoiceNumber ?? null))
                        this.searchInvoice();
                    @endif
                },

                focusScanner() {
                    if (this.$refs.invoiceScanner) {
                        this.$refs.invoiceScanner.focus();
                    }
                },

                async searchInvoice() {
                    const code = this.invoiceNumber.trim();
                    if (!code) return;

                    this.searching = true;
                    this.searchError = '';
                    this.sale = null;
                    this.items = [];

                    try {
                        const invoiceValue = code || 'DEFAULT';
                        const response = await fetch(
                            `{{ route('sales.returns.search', ['invoiceNumber' => ':invoiceNumber']) }}`
                            .replace(':invoiceNumber', encodeURIComponent(invoiceValue)), {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            }
                        );

                        const data = await response.json();

                        if (!response.ok || !data.success) {
                            this.searchError = data.message ||
                                'No sale found with that invoice number.';
                            this.searching = false;
                            return;
                        }

                        this.sale = data.sale;
                        this.items = (data.sale.sale_items || []).map(si => ({
                            sale_item_id: si.id,
                            product_id: si.product?.id || 0,
                            product_name: si.product?.name || 'Deleted Product',
                            sold_qty: parseInt(si.qty) || 0,
                            already_returned_qty: parseInt(si.already_returned_qty) ||
                                0,
                            max_returnable_qty: parseInt(si.max_returnable_qty) || 0,
                            rate: parseFloat(si.after_discount_price ?? si.rate) || 0,
                            return_qty: 0
                        }));

                        this.refundedAmountTouched = false;
                        this.refundedAmount = 0;
                        this.receivedAmount = this.calculateTotalReturnAmount();

                    } catch (e) {
                        this.searchError =
                            'Network error — please check your connection and try again.';
                    } finally {
                        this.searching = false;
                    }
                },

                clampReturnQty(index) {
                    let item = this.items[index];
                    let max = item.max_returnable_qty;
                    let val = parseInt(item.return_qty);

                    if (isNaN(val) || val < 0) val = 0;
                    if (val > max) val = max;

                    item.return_qty = val;
                },

                calculateTotalReturnAmount() {
                    return this.items.reduce((sum, item) => {
                        return sum + ((parseInt(item.return_qty) || 0) * (parseFloat(item
                            .rate) || 0));
                    }, 0);
                },

                async submitReturn() {
                    this.submitError = '';

                    const selected = this.items.filter(i => (parseInt(i.return_qty) || 0) > 0);

                    if (selected.length === 0) {
                        this.submitError =
                            'Enter a return quantity greater than 0 for at least one product.';
                        return;
                    }

                    for (const item of selected) {
                        const qty = parseInt(item.return_qty) || 0;
                        if (qty <= 0 || qty > item.max_returnable_qty) {
                            this.submitError =
                                `Return quantity for "${item.product_name}" must be between 1 and ${item.max_returnable_qty}.`;
                            return;
                        }
                    }

                    this.submitting = true;

                    try {
                        const response = await fetch("{{ route('sales.returns.store') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]')?.content ?? '',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({
                                items: selected.map(i => ({
                                    sale_item_id: i.sale_item_id,
                                    product_id: i.product_id,
                                    return_qty: i.return_qty,
                                })),
                                sale_id: this.sale.id,
                                notes: this.notes,
                                date: this.returnDate,
                                refunded_amount: this.refundedAmount,
                                total_amount: parseFloat(this.calculateTotalReturnAmount()
                                    .toFixed(2))
                            })
                        });

                        const data = await response.json();

                        if (!response.ok || !data.success) {
                            this.submitError = data.message ||
                                (data.errors ? Object.values(data.errors).flat().join(' ') :
                                    null) ||
                                'Something went wrong while processing the return.';
                            this.submitting = false;
                            return;
                        }

                        if (window.Toast) {
                            Toast.fire({
                                icon: 'success',
                                title: data.message
                            });
                        }

                        window.location.href = data.redirect;

                    } catch (e) {
                        this.submitError =
                            'Network error — please check your connection and try again.';
                        this.submitting = false;
                    }
                },

                formatMoney(amount) {
                    return parseFloat(amount || 0).toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                },

                formatDate(dateString) {
                    if (!dateString) return '—';
                    const date = new Date(dateString);
                    return date.toLocaleDateString('en-US', {
                        month: 'short',
                        day: '2-digit',
                        year: 'numeric'
                    });
                }
            }));
        });
    </script>
@endsection
