@extends('layouts.master')

@section('title', 'Sale Return Details')

@section('content')
    <div class="space-y-6">

        <!-- Header Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-muted">Point of Sale</span>
                <h1 class="font-display text-2xl font-bold text-forest">
                    Return History — <span class="font-mono">{{ $sale->invoice_number }}</span>
                </h1>
            </div>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('sales.returns.create', $sale->invoice_number) }}"
                    class="inline-flex items-center gap-2 rounded-[var(--radius-s)] bg-forest px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-forest-light transition-all">
                    <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Return More From This Invoice
                </a>
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

        <!-- Original Sale Summary -->
        <div class="panel bg-white">
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-muted mb-1">Sale Date</p>
                    <p class="text-sm font-mono font-medium">{{ $sale->date->format('d M, Y') }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-muted mb-1">Payment Method</p>
                    <p class="text-sm font-mono font-medium uppercase">{{ $sale->payment_method }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-muted mb-1">Original Net Total</p>
                    <p class="text-sm font-mono font-bold text-ink">Rs {{ number_format($sale->net_amount, 2) }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-muted mb-1">Total Refunded</p>
                    <p class="text-sm font-mono font-bold text-tag-red">
                        Rs {{ number_format($sale->saleReturns->sum('refunded_amount'), 2) }}
                    </p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-muted mb-1">Return Events</p>
                    <p class="text-sm font-mono font-bold text-forest">{{ $sale->saleReturns->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Current Item Status -->
        <div class="panel bg-white p-0 overflow-hidden">
            <div class="p-4 bg-paper border-b border-line">
                <h2 class="text-xs font-bold uppercase tracking-wider text-forest">Current Item Status</h2>
                <p class="text-[11px] text-muted">Available quantity accounts for every return recorded against this invoice
                    so far.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-ink">
                    <thead
                        class="bg-paper/50 border-b border-line text-xs font-semibold uppercase tracking-wider text-muted">
                        <tr>
                            <th class="py-3 px-4 min-w-[200px]">Product</th>
                            <th class="py-3 px-4 w-28 text-center">Originally Sold</th>
                            <th class="py-3 px-4 w-28 text-center">Total Returned</th>
                            <th class="py-3 px-4 w-28 text-center">Available Now</th>
                            <th class="py-3 px-4 w-32 text-right">Unit Price</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @foreach ($sale->saleItems as $item)
                            <tr class="{{ $item->max_returnable_qty <= 0 ? 'opacity-50' : '' }}">
                                <td class="py-2.5 px-4 font-medium">{{ $item->product->name ?? 'Deleted Product' }}</td>
                                <td class="py-2.5 px-4 text-center font-mono">
                                    {{ rtrim(rtrim(number_format($item->qty, 2), '0'), '.') }}</td>
                                <td class="py-2.5 px-4 text-center font-mono text-muted">{{ $item->already_returned_qty }}
                                </td>
                                <td class="py-2.5 px-4 text-center">
                                    <span
                                        class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-semibold
                                        {{ $item->max_returnable_qty > 0 ? 'bg-forest-tint text-forest' : 'bg-line text-muted' }}">
                                        {{ $item->max_returnable_qty }}
                                    </span>
                                </td>
                                <td class="py-2.5 px-4 text-right font-mono">Rs
                                    {{ number_format($item->after_discount_price, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Return History, grouped by event / date -->
        <div class="space-y-4">
            <h2 class="text-xs font-bold uppercase tracking-wider text-forest px-1">
                Return Events ({{ $sale->saleReturns->count() }})
            </h2>

            @forelse ($sale->saleReturns->sortByDesc('date') as $return)
                <div
                    class="panel bg-white p-0 overflow-hidden {{ $return->id === $saleReturn->id ? 'ring-2 ring-forest' : '' }}">
                    <div
                        class="p-4 bg-paper border-b border-line flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider text-muted">
                                    Return #{{ str_pad($return->id, 6, '0', STR_PAD_LEFT) }}
                                </p>
                                <p class="text-sm font-mono font-bold text-forest">
                                    {{ \Carbon\Carbon::parse($return->date)->format('d M, Y h:i A') }}
                                </p>
                            </div>
                            @if ($return->id === $saleReturn->id)
                                <span
                                    class="inline-flex items-center rounded-full bg-forest text-white px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wide">
                                    Viewing
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="text-right">
                                <p class="text-[10px] font-semibold uppercase tracking-wider text-muted">Refunded</p>
                                <p class="text-sm font-mono font-bold text-tag-red">Rs
                                    {{ number_format($return->refunded_amount, 2) }}</p>
                            </div>
                            <a href="{{ route('sales.bill.saleReturn.receipt', $return->id) }}"
                                class="inline-flex items-center gap-1.5 rounded-[var(--radius-s)] border border-line bg-white px-3 py-1.5 text-xs font-semibold text-ink hover:bg-paper transition-colors">
                                View Receipt
                            </a>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-ink">
                            <thead class="border-b border-line text-xs font-semibold uppercase tracking-wider text-muted">
                                <tr>
                                    <th class="py-2.5 px-4 min-w-[200px]">Product</th>
                                    <th class="py-2.5 px-4 w-28 text-center">Qty Returned</th>
                                    <th class="py-2.5 px-4 w-32 text-right">Rate</th>
                                    <th class="py-2.5 px-4 w-36 text-right">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-line">
                                @foreach ($return->saleReturnItems as $returnItem)
                                    <tr>
                                        <td class="py-2 px-4">{{ $returnItem->product->name ?? 'Deleted Product' }}</td>
                                        <td class="py-2 px-4 text-center font-mono">
                                            {{ rtrim(rtrim(number_format($returnItem->qty, 2), '0'), '.') }}</td>
                                        <td class="py-2 px-4 text-right font-mono">Rs
                                            {{ number_format($returnItem->rate, 2) }}</td>
                                        <td class="py-2 px-4 text-right font-mono font-semibold text-forest">Rs
                                            {{ number_format($returnItem->amount, 2) }}</td>
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
                </div>
            @empty
                <div class="panel bg-white text-center text-muted text-sm py-8">
                    No returns have been recorded for this invoice yet.
                </div>
            @endforelse
        </div>

    </div>
@endsection
