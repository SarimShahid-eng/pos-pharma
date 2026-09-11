@extends('layouts.master')

@section('title', 'Sales Report')

@section('content')
    <div class="space-y-6">

        <!-- Header Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-muted">Reports</span>
                <h1 class="font-display text-2xl font-bold text-forest">Sales Report</h1>
            </div>
            <a href="{{ route('reports.sales.export', request()->query()) }}"
                class="inline-flex items-center gap-2 rounded-[var(--radius-s)] bg-forest px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-forest-light transition-all">
                <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export PDF
            </a>
        </div>

        <!-- Date Range Filter -->
        <div class="panel bg-white">
            <form method="GET" action="{{ route('reports.sales.index') }}"
                class="flex flex-col sm:flex-row sm:items-end gap-4">
                <x-date-range-select :from="$fromDate" :to="$toDate" />
                <button type="submit"
                    class="rounded-[var(--radius-s)] bg-forest px-5 py-2 text-xs font-semibold text-white shadow-sm hover:bg-forest-light transition-all">
                    Apply
                </button>
                @if (request('from_date') && request('to_date'))
                    <a href="{{ route('reports.sales.index') }}"
                        class="rounded-[var(--radius-s)] border border-line bg-white px-3 py-2 text-xs font-semibold text-muted hover:bg-paper transition-colors">
                        Clear
                    </a>
                @endif
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="panel bg-white">
                <p class="text-xs font-semibold uppercase tracking-wider text-muted mb-1">Gross Sales</p>
                <p class="font-mono text-xl font-bold text-ink">Rs {{ number_format($totalSalesGross, 2) }}</p>
                <p class="text-[11px] text-muted mt-1">{{ $sales->count() }} invoice(s)</p>
            </div>
            <div class="panel bg-white">
                <p class="text-xs font-semibold uppercase tracking-wider text-muted mb-1">Sale Returns</p>
                <p class="font-mono text-xl font-bold text-tag-red">Rs {{ number_format($totalSaleReturnsValue, 2) }}</p>
                <p class="text-[11px] text-muted mt-1">{{ $saleReturns->count() }} return(s)</p>
            </div>
            <div class="panel bg-white">
                <p class="text-xs font-semibold uppercase tracking-wider text-muted mb-1">Refunded</p>
                <p class="font-mono text-xl font-bold text-tag-red">Rs {{ number_format($totalRefunded, 2) }}</p>
                <p class="text-[11px] text-muted mt-1">Actual cash/credit given back</p>
            </div>
            <div class="panel bg-white border-forest">
                <p class="text-xs font-semibold uppercase tracking-wider text-muted mb-1">Net Sales</p>
                <p class="font-mono text-xl font-bold text-forest">Rs {{ number_format($netSales, 2) }}</p>
                <p class="text-[11px] text-muted mt-1">Net total minus refunds</p>
            </div>
        </div>

        <!-- Sales Detail -->
        <div class="panel bg-white p-0 overflow-hidden">
            <div class="p-4 bg-paper border-b border-line">
                <h2 class="text-xs font-bold uppercase tracking-wider text-forest">Sales ({{ $sales->count() }})</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-ink">
                    <thead
                        class="bg-paper/50 border-b border-line text-xs font-semibold uppercase tracking-wider text-muted">
                        <tr>
                            <th class="py-3 px-4">Invoice #</th>
                            <th class="py-3 px-4">Date</th>
                            <th class="py-3 px-4">Payment</th>
                            <th class="py-3 px-4 text-right">Gross</th>
                            <th class="py-3 px-4 text-right">Discount</th>
                            <th class="py-3 px-4 text-right">Net Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @forelse ($sales as $sale)
                            <tr class="hover:bg-paper/50 transition-colors">
                                <td class="py-2.5 px-4 font-mono text-xs font-bold text-forest">{{ $sale->invoice_number }}
                                </td>
                                <td class="py-2.5 px-4 font-mono text-xs text-muted">
                                    {{ \Carbon\Carbon::parse($sale->date)->format('d M, Y h:i A') }}</td>
                                <td class="py-2.5 px-4 text-xs uppercase">{{ $sale->payment_method }}</td>
                                <td class="py-2.5 px-4 text-right font-mono text-xs">Rs
                                    {{ number_format($sale->total_amount, 2) }}</td>
                                <td class="py-2.5 px-4 text-right font-mono text-xs text-tag-red">
                                    {{ $sale->discount_amount > 0 ? '-Rs ' . number_format($sale->discount_amount, 2) : '—' }}
                                </td>
                                <td class="py-2.5 px-4 text-right font-mono text-xs font-bold">Rs
                                    {{ number_format($sale->net_amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-muted">No sales recorded in this period.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if ($sales->isNotEmpty())
                        <tfoot>
                            <tr class="bg-forest-tint/40 border-t-2 border-forest">
                                <td class="py-3 px-4 font-bold text-forest" colspan="3">Totals</td>
                                <td class="py-3 px-4 text-right font-mono font-bold">Rs
                                    {{ number_format($totalSalesGross, 2) }}</td>
                                <td class="py-3 px-4 text-right font-mono font-bold text-tag-red">-Rs
                                    {{ number_format($totalSalesDiscount, 2) }}</td>
                                <td class="py-3 px-4 text-right font-mono font-bold text-forest">Rs
                                    {{ number_format($totalSalesNet, 2) }}</td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>

        <!-- Sale Returns Detail -->
        <div class="panel bg-white p-0 overflow-hidden">
            <div class="p-4 bg-paper border-b border-line">
                <h2 class="text-xs font-bold uppercase tracking-wider text-forest">Sale Returns
                    ({{ $saleReturns->count() }})</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-ink">
                    <thead
                        class="bg-paper/50 border-b border-line text-xs font-semibold uppercase tracking-wider text-muted">
                        <tr>
                            <th class="py-3 px-4">Return #</th>
                            <th class="py-3 px-4">Orig. Invoice</th>
                            <th class="py-3 px-4">Date</th>
                            <th class="py-3 px-4 text-right">Return Value</th>
                            <th class="py-3 px-4 text-right">Refunded</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @forelse ($saleReturns as $return)
                            <tr class="hover:bg-paper/50 transition-colors">
                                <td class="py-2.5 px-4 font-mono text-xs font-bold text-tag-red">
                                    #{{ str_pad($return->id, 6, '0', STR_PAD_LEFT) }}</td>
                                <td class="py-2.5 px-4 font-mono text-xs">{{ $return->sale->invoice_number ?? '—' }}</td>
                                <td class="py-2.5 px-4 font-mono text-xs text-muted">
                                    {{ \Carbon\Carbon::parse($return->date)->format('d M, Y h:i A') }}</td>
                                <td class="py-2.5 px-4 text-right font-mono text-xs">Rs
                                    {{ number_format($return->total_amount, 2) }}</td>
                                <td class="py-2.5 px-4 text-right font-mono text-xs font-bold text-tag-red">Rs
                                    {{ number_format($return->refunded_amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-muted">No sale returns recorded in this
                                    period.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if ($saleReturns->isNotEmpty())
                        <tfoot>
                            <tr class="bg-tag-red-tint/40 border-t-2 border-tag-red">
                                <td class="py-3 px-4 font-bold text-tag-red" colspan="3">Totals</td>
                                <td class="py-3 px-4 text-right font-mono font-bold">Rs
                                    {{ number_format($totalSaleReturnsValue, 2) }}</td>
                                <td class="py-3 px-4 text-right font-mono font-bold text-tag-red">Rs
                                    {{ number_format($totalRefunded, 2) }}</td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>

    </div>
@endsection
