@extends('layouts.master')

@section('title', 'Sales List')

@section('content')
    <div x-data="saleIndex()" class="space-y-6">
        <x-toast />

        <!-- Top Action & Navigation Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-muted">Point of Sale</span>
                <h1 class="font-display text-2xl font-bold text-forest">Sales</h1>
            </div>
            <div class="flex items-center gap-2.5">
                {{-- <x-export-button route="sales.index" /> --}}

                <a href="{{ route('sales.create') }}"
                    class="inline-flex items-center gap-2 rounded-[var(--radius-s)] bg-forest px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-forest-light transition-all">
                    <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    New Sale
                </a>
            </div>
        </div>

        <x-search-bar-filter :route="route('sales.index')" placeholder="Search by invoice number...">

            {{-- Slot for Select2 Dropdowns / Filters --}}
            <x-slot:filters>
                <div class="w-full sm:w-48">
                    <label for="supplier_id" class="text-xs">Search by Methods</label>
                    {{-- Adjust these option values to match your Sale.payment_method enum --}}
                    <select name="payment_method"
                        class="w-full rounded-[var(--radius-s)] border border-line bg-paper px-3.5 py-2 text-sm text-ink focus:border-forest focus:bg-white focus:outline-none focus:ring-1 focus:ring-forest transition-colors">
                        <option value="">All Payment Methods</option>
                        <option value="cash" {{ request('payment_method') === 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="bank" {{ request('payment_method') === 'bank' ? 'selected' : '' }}>Bank</option>
                        <option value="online" {{ request('payment_method') === 'card' ? 'selected' : '' }}>Card
                        </option>
                    </select>
                </div>
                <div>
                    <label for="supplier_id" class="text-xs">From Date</label>
                    <input type="date" name="from_date" id="from_date" value="{{ request('from_date', '') }}"
                        class="w-full rounded-[var(--radius-s)] border  bg-paper px-3.5 py-2 text-sm text-ink focus:border-forest focus:bg-white focus:outline-none focus:ring-1 focus:ring-forest transition-colors">
                </div>
                <div>
                    <label for="supplier_id" class="text-xs">To Date</label>

                    <input type="date" name="to_date" id="to_date" value="{{ request('to_date', '') }}"
                        class="w-full rounded-[var(--radius-s)] border  bg-paper px-3.5 py-2 text-sm text-ink focus:border-forest focus:bg-white focus:outline-none focus:ring-1 focus:ring-forest transition-colors">
                </div>
            </x-slot:filters>

            {{-- Slot for Action Buttons (Export, Print, Add New) --}}
            {{-- <x-slot:actions>
                <a href="{{ route('sales.index', array_merge(request()->query(), ['export' => 'csv'])) }}"
                    class="inline-flex items-center gap-2 rounded-[var(--radius-s)] border border-line bg-white px-3 py-2 text-xs font-semibold text-ink hover:bg-paper transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export CSV
                </a>
            </x-slot:actions> --}}

        </x-search-bar-filter>

        <!-- Sales Table -->
        <div class="panel bg-white p-0 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-ink">
                    <thead class="bg-paper border-b border-line text-xs font-semibold uppercase tracking-wider text-muted">
                        <tr>
                            <th class="py-3 px-4">Invoice #</th>
                            <th class="py-3 px-4">Date</th>
                            <th class="py-3 px-4 text-center">Items</th>
                            <th class="py-3 px-4">Payment Method</th>
                            <th class="py-3 px-4 text-right">Net Total</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @forelse($sales as $sale)
                            <tr class="hover:bg-paper/50 transition-colors">
                                <!-- Invoice Number -->
                                <td class="py-3 px-4 font-mono text-xs font-bold text-forest">
                                    {{ $sale->invoice_number }}
                                </td>

                                <!-- Date -->
                                <td class="py-3 px-4 font-mono text-xs text-muted">
                                    {{ $sale->formatted_date }}
                                </td>

                                <!-- Total Items -->
                                <td class="py-3 px-4 text-center font-mono text-xs text-ink font-semibold">
                                    {{ $sale->saleItems->sum('qty') }}
                                </td>

                                <!-- Payment Method -->
                                <td class="py-3 px-4">
                                    <span
                                        class="inline-flex items-center rounded-full bg-forest-tint/50 px-2.5 py-1 text-[11px] font-semibold uppercase tracking-wide text-forest">
                                        {{ $sale->payment_method }}
                                    </span>
                                </td>

                                <!-- Net Total -->
                                <td class="py-3 px-4 text-right font-mono text-xs font-bold text-ink">
                                    Rs {{ number_format($sale->net_amount, 2) }}
                                </td>

                                <!-- Actions -->
                                <td class="py-3 px-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">
                                        <button type="button"
                                            @click="openModal({{ json_encode($sale->load(['saleItems.product'])) }})"
                                            class="rounded-[var(--radius-s)] border border-line bg-white p-1.5 text-muted hover:text-forest hover:border-forest transition-colors"
                                            title="View Details">
                                            <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                        @if (!$sale->has_returns)
                                            <a href="{{ route('sales.edit', $sale->id) }}"
                                                class="rounded-[var(--radius-s)] border border-line bg-white p-1.5 text-muted hover:text-forest hover:border-forest transition-colors"
                                                title="Edit Sale">
                                                <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                        @else
                                            <span class="badge bg-secondary" title="Editing disabled for returned sales">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor"
                                                    class="w-4 h-4 stroke-current fill-none">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" />
                                                </svg>
                                            </span>
                                        @endif
                                        <!-- Return Sale -->
                                        {{-- <button type="button"
                                            @click="openReturnModal({{ json_encode($sale->load(['saleItems.product'])) }})"
                                            class="rounded-[var(--radius-s)] border border-line bg-white p-1.5 text-muted hover:text-tag-red hover:border-tag-red transition-colors"
                                            title="Return Items">
                                            <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 15L4 10m0 0l5-5m-5 5h11a4 4 0 010 8h-1" />
                                            </svg>
                                        </button> --}}
                                        <a href="{{ route('sales.returns.create', ['invoiceNumber' => $sale->invoice_number]) }}"
                                            target="_blank"
                                            class="rounded-[var(--radius-s)] border border-line bg-white p-1.5 text-muted hover:text-tag-red hover:border-tag-red transition-colors"
                                            title="Return Items">
                                            <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 15L4 10m0 0l5-5m-5 5h11a4 4 0 010 8h-1" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('sales.bill.index', ['invoiceNumber' => $sale->invoice_number]) }}"
                                            class="rounded-[var(--radius-s)] border border-line bg-white p-1.5 text-muted hover:text-tag-red hover:border-tag-red transition-colors"
                                            title="Bill"
                                            target="_blank"
                                            >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor"
                                                class="w-4 h-4 stroke-current fill-none">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m9 14.25 6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0c1.1.128 1.907 1.077 1.907 2.185ZM9.75 9h.008v.008H9.75V9Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm4.125 4.5h.008v.008h-.008V13.5Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                            </svg>

                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-muted">
                                    <p class="text-sm">No sales found matching your query.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Footer -->
            @if ($sales->hasPages())
                <div class="px-4 py-3 border-t border-line bg-paper">
                    {{ $sales->withQueryString()->links() }}
                </div>
            @endif
        </div>

        @include('sales.modal')
        @include('sales.return_modal')

    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('saleIndex', () => ({
                showModal: false,
                activeSale: null,
                showReturnModal: false,
                returnSale: null,
                returnItems: [],
                returnReason: '',
                returnDate: '',
                returnError: '',
                returnSuccess: false,
                returnSubmitting: false,
                returnFormAction: '',

                openModal(sale) {
                    this.activeSale = sale;
                    console.log(sale)
                    this.showModal = true;
                },

                closeModal() {
                    this.showModal = false;
                    this.activeSale = null;
                },

                openReturnModal(sale) {
                    this.returnSale = sale;
                    this.returnError = '';
                    this.returnSuccess = false;
                    this.returnSubmitting = false;
                    this.returnReason = sale?.sale_return ? sale.sale_return.notes : '';
                    this.returnDate = sale?.sale_return ? sale.sale_return.return_date : new Date()
                        .toISOString().split('T')[0];

                    this.returnItems = (sale.sale_items || []).map(si => ({
                        sale_item_id: si.id,
                        product_id: si.product?.id || 0,
                        product_name: si.product?.name || 'Unknown Product',
                        sold_qty: parseInt(si.qty) || 0,
                        already_returned_qty: si.already_returned_qty || 0,
                        max_returnable_qty: si.max_returnable_qty || 0,
                        return_qty: si.already_returned_qty || 0
                    }));

                    // Adjust this URL to match your backend route.
                    this.returnFormAction = "{{ route('sales.returns.store') }}";
                    this.showReturnModal = true;
                },

                closeReturnModal() {
                    if (this.returnSubmitting) return; // don't let them close mid-request
                    this.showReturnModal = false;
                    this.returnSale = null;
                    this.returnItems = [];
                    this.returnError = '';
                    this.returnSuccess = false;
                },

                // Keeps each return qty within [0, sold_qty] as the user types
                clampReturnQty(index) {
                    let item = this.returnItems[index];
                    let max = item.sold_qty;
                    let val = parseInt(item.return_qty);

                    if (isNaN(val) || val < 0) val = 0;
                    if (val > max) val = max;

                    item.return_qty = val;
                    item.remaining_qty = max - val;
                },

                calculateRemainingQuantity(index) {
                    let item = this.returnItems[index];
                    if (!item) return 0;

                    let sold = parseInt(item.sold_qty) || 0;
                    let returning = parseInt(item.return_qty) || 0;

                    return Math.max(0, sold - returning);
                },

                async submitReturn() {
                    this.returnError = '';
                    this.returnSuccess = false;

                    const selected = this.returnItems.filter(i => (parseInt(i.return_qty) || 0) >
                        0);

                    if (selected.length === 0) {
                        this.returnError =
                            'Enter a return quantity greater than 0 for at least one product.';
                        return;
                    }

                    for (const item of selected) {
                        const qty = parseInt(item.return_qty) || 0;
                        if (qty <= 0 || qty > item.sold_qty) {
                            this.returnError =
                                `Return quantity for "${item.product_name}" must be between 1 and ${item.sold_qty}.`;
                            return;
                        }
                    }

                    this.returnSubmitting = true;

                    try {
                        const response = await fetch(this.returnFormAction, {
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
                                sale_id: this.returnSale.id,
                                reason: this.returnReason,
                                date: this.returnDate
                            })
                        });

                        const data = await response.json();

                        if (!response.ok || !data.success) {
                            this.returnError = data.message ||
                                (data.errors ? Object.values(data.errors).flat().join(' ') :
                                    null) ||
                                'Something went wrong while processing the return.';
                            this.returnSubmitting = false;
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
                        this.returnError =
                            'Network error — please check your connection and try again.';
                        this.returnSubmitting = false;
                    }
                },

                calculateTotalQty() {
                    if (!this.activeSale?.sale_items) return 0;
                    return this.activeSale.sale_items.reduce((sum, item) => sum + parseInt(item.qty ||
                        0), 0);
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
                        }) +
                        ' ' + date.toLocaleTimeString('en-US', {
                            hour: '2-digit',
                            minute: '2-digit'
                        });
                }
            }));
        });
    </script>
@endsection
