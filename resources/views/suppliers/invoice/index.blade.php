@extends('layouts.master')

@section('title', 'Supplier Invoice')

@section('content')
    <div x-data="supplierInvoiceFilters()" class="space-y-6">

        <!-- Header Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-muted">Accounts &middot; Supplier</span>
                <h1 class="font-display text-2xl font-bold text-forest">Supplier Invoice</h1>
            </div>
            @if ($supplier)
                <a href="{{ route('suppliers.invoice.export', request()->query()) }}"
                    class="inline-flex items-center gap-2 rounded-[var(--radius-s)] bg-forest px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-forest-light transition-all">
                    <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export PDF
                </a>
            @endif
        </div>

        <!-- Search Filters -->
        <div class="panel bg-white">
            <form method="GET" action="{{ route('suppliers.invoice.index') }}"
                class="flex flex-col sm:flex-row sm:items-end gap-4">
                <div class="flex-1 sm:max-w-xs">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-muted mb-1">Supplier</label>
                    <select name="supplier_id" x-model="supplierId" x-select2="supplierId" placeholder="Search supplier..."
                        class="w-full">
                        <option value="">Select Supplier</option>
                        @foreach ($suppliers as $s)
                            <option value="{{ $s->id }}"
                                {{ $supplier && $supplier->id === $s->id ? 'selected' : '' }}>
                                {{ $s->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-muted mb-1">From Date</label>
                    <input type="date" name="from_date" value="{{ $fromDate }}"
                        class="w-full rounded-[var(--radius-s)] border border-line bg-paper px-3.5 py-2 text-sm text-ink focus:border-forest focus:bg-white focus:outline-none focus:ring-1 focus:ring-forest transition-colors">
                </div>
                <div class="flex-1">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-muted mb-1">To Date</label>
                    <input type="date" name="to_date" value="{{ $toDate }}"
                        class="w-full rounded-[var(--radius-s)] border border-line bg-paper px-3.5 py-2 text-sm text-ink focus:border-forest focus:bg-white focus:outline-none focus:ring-1 focus:ring-forest transition-colors">
                </div>
                <button type="submit"
                    class="rounded-[var(--radius-s)] bg-forest px-5 py-2 text-xs font-semibold text-white shadow-sm hover:bg-forest-light transition-all">
                    Search
                </button>
            </form>
        </div>

        @if (!$supplier)
            <div class="panel bg-white text-center text-muted text-sm py-10">
                Select a supplier and date range above, then hit Search to view their invoice.
            </div>
        @else
            <!-- Supplier + Account Summary -->
            <div class="panel bg-white">
                <div
                    class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4 pb-4 border-b border-line">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-muted mb-1">Supplier</p>
                        <p class="text-lg font-bold text-forest">{{ $supplier->name }}</p>
                        <p class="text-xs font-mono text-muted">{{ $supplier->phone_number ?? 'No phone on file' }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-semibold uppercase tracking-wider text-muted mb-1">Period</p>
                        <p class="text-sm font-mono font-medium">
                            {{ \Carbon\Carbon::parse($fromDate)->format('d M Y') }} &ndash;
                            {{ \Carbon\Carbon::parse($toDate)->format('d M Y') }}
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-muted mb-1">Opening Balance</p>
                        <p
                            class="font-mono text-base font-bold {{ $openingBalance >= 0 ? 'text-tag-red' : 'text-forest' }}">
                            Rs {{ number_format(abs($openingBalance), 2) }} {{ $openingBalance >= 0 ? 'Cr' : 'Dr' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-muted mb-1">Total Payments</p>
                        <p class="font-mono text-base font-bold text-forest">Rs {{ number_format($totalDebit, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-muted mb-1">Total Purchases</p>
                        <p class="font-mono text-base font-bold text-tag-red">Rs {{ number_format($totalCredit, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-muted mb-1">Closing Balance</p>
                        <p
                            class="font-mono text-base font-bold {{ $closingBalance >= 0 ? 'text-tag-red' : 'text-forest' }}">
                            Rs {{ number_format(abs($closingBalance), 2) }} {{ $closingBalance >= 0 ? 'Cr' : 'Dr' }}
                        </p>
                    </div>
                </div>
            </div>

            @if ($purchases->isEmpty() && $purchaseReturns->isEmpty())
                <div class="panel bg-white text-center text-muted text-sm py-10">
                    No purchases or returns were recorded for this supplier in the selected period.
                </div>
            @endif

            <!-- Purchases -->
            @if ($purchases->isNotEmpty())
                <div class="space-y-4">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-forest px-1">
                        Purchases ({{ $purchases->count() }})
                    </h2>

                    @foreach ($purchases as $purchase)
                        @php $gross = (float) $purchase->total_amount + (float) $purchase->discount_amount; @endphp
                        <div class="panel bg-white p-0 overflow-hidden">
                            <div
                                class="p-4 bg-paper border-b border-line flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wider text-muted">
                                        {{ \Carbon\Carbon::parse($purchase->date)->format('d M, Y') }}
                                    </p>
                                    <p class="text-sm font-mono font-bold text-forest">
                                        {{ $purchase->invoice_number }}
                                        @if ($purchase->reference_number)
                                            <span class="text-muted font-normal">&middot; Ref:
                                                {{ $purchase->reference_number }}</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-[10px] font-semibold uppercase tracking-wider text-muted">Net Total</p>
                                    <p class="text-sm font-mono font-bold text-ink">Rs
                                        {{ number_format($purchase->total_amount, 2) }}</p>
                                </div>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm text-ink">
                                    <thead
                                        class="border-b border-line text-xs font-semibold uppercase tracking-wider text-muted">
                                        <tr>
                                            <th class="py-2.5 px-4 min-w-[200px]">Product</th>
                                            <th class="py-2.5 px-4 w-24 text-center">Qty</th>
                                            <th class="py-2.5 px-4 w-32 text-right">Unit Cost</th>
                                            <th class="py-2.5 px-4 w-28 text-right">Discount</th>
                                            <th class="py-2.5 px-4 w-36 text-right">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-line">
                                        @foreach ($purchase->purchaseItems as $item)
                                            <tr>
                                                <td class="py-2 px-4">{{ $item->product->name ?? 'Deleted Product' }}</td>
                                                <td class="py-2 px-4 text-center font-mono">
                                                    {{ rtrim(rtrim(number_format($item->qty, 2), '0'), '.') }}</td>
                                                <td class="py-2 px-4 text-right font-mono">Rs
                                                    {{ number_format($item->unit_cost, 2) }}</td>
                                                <td class="py-2 px-4 text-right font-mono">
                                                    {{ $item->discount_amount > 0 ? '-Rs ' . number_format($item->discount_amount, 2) : '—' }}
                                                </td>
                                                <td class="py-2 px-4 text-right font-mono font-semibold text-forest">Rs
                                                    {{ number_format($item->subtotal_amount, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="px-4 py-3 border-t border-line bg-paper/50 flex justify-end gap-6 text-xs">
                                <span class="text-muted">Gross: <span class="font-mono font-semibold text-ink">Rs
                                        {{ number_format($gross, 2) }}</span></span>
                                @if ($purchase->discount_amount > 0)
                                    <span class="text-muted">Discount: <span
                                            class="font-mono font-semibold text-tag-red">-Rs
                                            {{ number_format($purchase->discount_amount, 2) }}</span></span>
                                @endif
                                <span class="text-muted">Net: <span class="font-mono font-bold text-forest">Rs
                                        {{ number_format($purchase->total_amount, 2) }}</span></span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Purchase Returns -->
            @if ($purchaseReturns->isNotEmpty())
                <div class="space-y-4">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-forest px-1">
                        Purchase Returns ({{ $purchaseReturns->count() }})
                    </h2>

                    @foreach ($purchaseReturns as $return)
                        <div class="panel bg-white p-0 overflow-hidden">
                            <div
                                class="p-4 bg-paper border-b border-line flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wider text-muted">
                                        {{ \Carbon\Carbon::parse($return->date)->format('d M, Y') }}
                                    </p>
                                    <p class="text-sm font-mono font-bold text-tag-red">
                                        Return #{{ str_pad($return->id, 6, '0', STR_PAD_LEFT) }}
                                        <span class="text-muted font-normal">&middot; Orig. Invoice:
                                            {{ $return->purchase->invoice_number ?? '—' }}</span>
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-[10px] font-semibold uppercase tracking-wider text-muted">Refunded</p>
                                    <p class="text-sm font-mono font-bold text-tag-red">Rs
                                        {{ number_format($return->received_amount, 2) }}</p>
                                </div>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm text-ink">
                                    <thead
                                        class="border-b border-line text-xs font-semibold uppercase tracking-wider text-muted">
                                        <tr>
                                            <th class="py-2.5 px-4 min-w-[200px]">Product</th>
                                            <th class="py-2.5 px-4 w-28 text-center">Qty Returned</th>
                                            <th class="py-2.5 px-4 w-32 text-right">Unit Cost</th>
                                            <th class="py-2.5 px-4 w-36 text-right">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-line">
                                        @foreach ($return->purchaseReturnItems as $item)
                                            <tr>
                                                <td class="py-2 px-4">{{ $item->product->name ?? 'Deleted Product' }}</td>
                                                <td class="py-2 px-4 text-center font-mono">
                                                    {{ rtrim(rtrim(number_format($item->qty, 2), '0'), '.') }}</td>
                                                <td class="py-2 px-4 text-right font-mono">Rs
                                                    {{ number_format($item->unit_cost, 2) }}</td>
                                                <td class="py-2 px-4 text-right font-mono font-semibold text-tag-red">Rs
                                                    {{ number_format($item->amount, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            @if ($return->notes)
                                <div class="px-4 py-3 border-t border-line bg-paper/50">
                                    <p class="text-xs text-muted"><span class="font-semibold text-ink">Note:</span>
                                        {{ $return->notes }}</p>
                                </div>
                            @endif

                            <div class="px-4 py-3 border-t border-line bg-paper/50 flex justify-end gap-6 text-xs">
                                <span class="text-muted">Return Value: <span class="font-mono font-semibold text-ink">Rs
                                        {{ number_format($return->total_amount, 2) }}</span></span>
                                @if ($return->total_amount > $return->received_amount)
                                    <span class="text-muted">Retained as Credit: <span
                                            class="font-mono font-semibold text-tag-red">-Rs
                                            {{ number_format($return->total_amount - $return->received_amount, 2) }}</span></span>
                                @endif
                                <span class="text-muted">Refunded: <span class="font-mono font-bold text-tag-red">Rs
                                        {{ number_format($return->received_amount, 2) }}</span></span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        @endif

    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('supplierInvoiceFilters', () => ({
                supplierId: @json($supplier->id ?? ''),
            }));
        });
    </script>
@endsection
