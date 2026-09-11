@extends('layouts.master')

@section('title', 'Profit Report')

@section('content')
    <div class="space-y-6">

        <!-- Header Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-muted">Reports</span>
                <h1 class="font-display text-2xl font-bold text-forest">Profit Report</h1>
            </div>
        </div>

        <!-- Date Range Filter -->
        <div class="panel bg-white">
            <form method="GET" action="{{ route('reports.profit.index') }}"
                class="flex flex-col sm:flex-row sm:items-end gap-4">
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
                    Apply
                </button>
                <a href="{{ route('reports.profit.index') }}"
                    class="rounded-[var(--radius-s)] border border-line bg-white px-4 py-2 text-xs font-semibold text-muted hover:bg-paper transition-colors">
                    Reset
                </a>
            </form>
        </div>

        <!-- Headline Profit Summary -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="panel bg-white">
                <p class="text-xs font-semibold uppercase tracking-wider text-muted mb-1">Net Sales Revenue</p>
                <p class="font-mono text-xl font-bold text-ink">Rs {{ number_format($totalNetRevenue, 2) }}</p>
                <p class="text-[11px] text-muted mt-1">After sale returns</p>
            </div>
            <div class="panel bg-white">
                <p class="text-xs font-semibold uppercase tracking-wider text-muted mb-1">Cost of Goods Sold</p>
                <p class="font-mono text-xl font-bold text-tag-red">Rs {{ number_format($totalCogs, 2) }}</p>
                <p class="text-[11px] text-muted mt-1">Estimated at current cost price</p>
            </div>
            <div class="panel bg-white border-forest">
                <p class="text-xs font-semibold uppercase tracking-wider text-muted mb-1">Gross Profit</p>
                <p class="font-mono text-xl font-bold {{ $grossProfit >= 0 ? 'text-forest' : 'text-tag-red' }}">
                    Rs {{ number_format($grossProfit, 2) }}
                </p>
                <p class="text-[11px] text-muted mt-1">Revenue minus COGS</p>
            </div>
            <div class="panel bg-white">
                <p class="text-xs font-semibold uppercase tracking-wider text-muted mb-1">Gross Margin</p>
                <p class="font-mono text-xl font-bold {{ $grossMargin >= 0 ? 'text-forest' : 'text-tag-red' }}">
                    {{ number_format($grossMargin, 1) }}%
                </p>
                <p class="text-[11px] text-muted mt-1">Profit as % of revenue</p>
            </div>
        </div>

        <!-- Secondary Context Summary -->
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="panel bg-white">
                <p class="text-xs font-semibold uppercase tracking-wider text-muted mb-1">Gross Sales</p>
                <p class="font-mono text-sm font-bold text-ink">Rs {{ number_format($totalSalesGross, 2) }}</p>
            </div>
            <div class="panel bg-white">
                <p class="text-xs font-semibold uppercase tracking-wider text-muted mb-1">Sale Returns</p>
                <p class="font-mono text-sm font-bold text-tag-red">Rs {{ number_format($totalSaleReturnsValue, 2) }}</p>
            </div>
            <div class="panel bg-white">
                <p class="text-xs font-semibold uppercase tracking-wider text-muted mb-1">Total Purchases</p>
                <p class="font-mono text-sm font-bold text-ink">Rs {{ number_format($totalPurchases, 2) }}</p>
            </div>
            <div class="panel bg-white">
                <p class="text-xs font-semibold uppercase tracking-wider text-muted mb-1">Purchase Returns</p>
                <p class="font-mono text-sm font-bold text-tag-red">Rs {{ number_format($totalPurchaseReturns, 2) }}</p>
            </div>
            <div class="panel bg-white">
                <p class="text-xs font-semibold uppercase tracking-wider text-muted mb-1">Net Purchases</p>
                <p class="font-mono text-sm font-bold text-forest">Rs {{ number_format($netPurchases, 2) }}</p>
            </div>
        </div>

        <!-- Product-wise Profit Breakdown -->
        <div class="panel bg-white p-0 overflow-hidden">
            <div class="p-4 bg-paper border-b border-line">
                <h2 class="text-xs font-bold uppercase tracking-wider text-forest">Product-wise Profit</h2>
                <p class="text-[11px] text-muted">Sorted by profit, highest first. Cost is estimated at each product's current cost price.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-ink">
                    <thead class="bg-paper/50 border-b border-line text-xs font-semibold uppercase tracking-wider text-muted">
                        <tr>
                            <th class="py-3 px-4 min-w-[200px]">Product</th>
                            <th class="py-3 px-4 text-center">Qty Sold</th>
                            <th class="py-3 px-4 text-center">Qty Returned</th>
                            <th class="py-3 px-4 text-center">Net Qty</th>
                            <th class="py-3 px-4 text-right">Net Revenue</th>
                            <th class="py-3 px-4 text-right">Cost (COGS)</th>
                            <th class="py-3 px-4 text-right">Profit</th>
                            <th class="py-3 px-4 text-right">Margin</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @forelse ($productRows as $row)
                            <tr class="hover:bg-paper/50 transition-colors">
                                <td class="py-2.5 px-4 font-medium">{{ $row['product']->name ?? 'Deleted Product' }}</td>
                                <td class="py-2.5 px-4 text-center font-mono">{{ rtrim(rtrim(number_format($row['qty_sold'], 2), '0'), '.') }}</td>
                                <td class="py-2.5 px-4 text-center font-mono text-muted">{{ rtrim(rtrim(number_format($row['qty_returned'], 2), '0'), '.') }}</td>
                                <td class="py-2.5 px-4 text-center font-mono font-semibold">{{ rtrim(rtrim(number_format($row['net_qty'], 2), '0'), '.') }}</td>
                                <td class="py-2.5 px-4 text-right font-mono">Rs {{ number_format($row['net_revenue'], 2) }}</td>
                                <td class="py-2.5 px-4 text-right font-mono text-tag-red">Rs {{ number_format($row['cogs'], 2) }}</td>
                                <td class="py-2.5 px-4 text-right font-mono font-bold {{ $row['profit'] >= 0 ? 'text-forest' : 'text-tag-red' }}">
                                    Rs {{ number_format($row['profit'], 2) }}
                                </td>
                                <td class="py-2.5 px-4 text-right font-mono {{ $row['margin'] >= 0 ? 'text-forest' : 'text-tag-red' }}">
                                    {{ number_format($row['margin'], 1) }}%
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-muted">No sales recorded in this period.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if ($productRows->isNotEmpty())
                        <tfoot>
                            <tr class="bg-forest-tint/40 border-t-2 border-forest">
                                <td class="py-3 px-4 font-bold text-forest" colspan="4">Totals</td>
                                <td class="py-3 px-4 text-right font-mono font-bold text-forest">Rs {{ number_format($totalNetRevenue, 2) }}</td>
                                <td class="py-3 px-4 text-right font-mono font-bold text-tag-red">Rs {{ number_format($totalCogs, 2) }}</td>
                                <td class="py-3 px-4 text-right font-mono font-bold {{ $grossProfit >= 0 ? 'text-forest' : 'text-tag-red' }}">
                                    Rs {{ number_format($grossProfit, 2) }}
                                </td>
                                <td class="py-3 px-4 text-right font-mono font-bold {{ $grossMargin >= 0 ? 'text-forest' : 'text-tag-red' }}">
                                    {{ number_format($grossMargin, 1) }}%
                                </td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>

        <!-- Sales Detail -->
        <div class="panel bg-white p-0 overflow-hidden">
            <div class="p-4 bg-paper border-b border-line">
                <h2 class="text-xs font-bold uppercase tracking-wider text-forest">Sales ({{ $sales->count() }})</h2>
            </div>
            <div class="overflow-x-auto max-h-96 overflow-y-auto">
                <table class="w-full text-left text-sm text-ink">
                    <thead class="bg-paper/50 border-b border-line text-xs font-semibold uppercase tracking-wider text-muted sticky top-0">
                        <tr>
                            <th class="py-2.5 px-4">Invoice #</th>
                            <th class="py-2.5 px-4">Date</th>
                            <th class="py-2.5 px-4">Payment</th>
                            <th class="py-2.5 px-4 text-right">Net Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @forelse ($sales as $sale)
                            <tr class="hover:bg-paper/50 transition-colors">
                                <td class="py-2 px-4 font-mono text-xs font-bold text-forest">{{ $sale->invoice_number }}</td>
                                <td class="py-2 px-4 font-mono text-xs text-muted">{{ \Carbon\Carbon::parse($sale->date)->format('d M, Y') }}</td>
                                <td class="py-2 px-4 text-xs uppercase">{{ $sale->payment_method }}</td>
                                <td class="py-2 px-4 text-right font-mono text-xs font-semibold">Rs {{ number_format($sale->net_amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-8 text-center text-muted">No sales in this period.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Purchases Detail -->
        <div class="panel bg-white p-0 overflow-hidden">
            <div class="p-4 bg-paper border-b border-line">
                <h2 class="text-xs font-bold uppercase tracking-wider text-forest">Purchases ({{ $purchases->count() }})</h2>
            </div>
            <div class="overflow-x-auto max-h-96 overflow-y-auto">
                <table class="w-full text-left text-sm text-ink">
                    <thead class="bg-paper/50 border-b border-line text-xs font-semibold uppercase tracking-wider text-muted sticky top-0">
                        <tr>
                            <th class="py-2.5 px-4">Invoice #</th>
                            <th class="py-2.5 px-4">Supplier</th>
                            <th class="py-2.5 px-4">Date</th>
                            <th class="py-2.5 px-4 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @forelse ($purchases as $purchase)
                            <tr class="hover:bg-paper/50 transition-colors">
                                <td class="py-2 px-4 font-mono text-xs font-bold text-forest">{{ $purchase->invoice_number }}</td>
                                <td class="py-2 px-4 text-xs">{{ $purchase->supplier->name ?? 'N/A' }}</td>
                                <td class="py-2 px-4 font-mono text-xs text-muted">{{ \Carbon\Carbon::parse($purchase->date)->format('d M, Y') }}</td>
                                <td class="py-2 px-4 text-right font-mono text-xs font-semibold">Rs {{ number_format($purchase->total_amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-8 text-center text-muted">No purchases in this period.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Returns Detail -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="panel bg-white p-0 overflow-hidden">
                <div class="p-4 bg-paper border-b border-line">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-forest">Sale Returns ({{ $saleReturns->count() }})</h2>
                </div>
                <div class="overflow-x-auto max-h-80 overflow-y-auto">
                    <table class="w-full text-left text-sm text-ink">
                        <thead class="bg-paper/50 border-b border-line text-xs font-semibold uppercase tracking-wider text-muted sticky top-0">
                            <tr>
                                <th class="py-2.5 px-4">Orig. Invoice</th>
                                <th class="py-2.5 px-4">Date</th>
                                <th class="py-2.5 px-4 text-right">Refunded</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line">
                            @forelse ($saleReturns as $return)
                                <tr class="hover:bg-paper/50 transition-colors">
                                    <td class="py-2 px-4 font-mono text-xs">{{ $return->sale->invoice_number ?? '—' }}</td>
                                    <td class="py-2 px-4 font-mono text-xs text-muted">{{ \Carbon\Carbon::parse($return->date)->format('d M, Y') }}</td>
                                    <td class="py-2 px-4 text-right font-mono text-xs font-semibold text-tag-red">Rs {{ number_format($return->refunded_amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="py-6 text-center text-muted">No sale returns in this period.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="panel bg-white p-0 overflow-hidden">
                <div class="p-4 bg-paper border-b border-line">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-forest">Purchase Returns ({{ $purchaseReturns->count() }})</h2>
                </div>
                <div class="overflow-x-auto max-h-80 overflow-y-auto">
                    <table class="w-full text-left text-sm text-ink">
                        <thead class="bg-paper/50 border-b border-line text-xs font-semibold uppercase tracking-wider text-muted sticky top-0">
                            <tr>
                                <th class="py-2.5 px-4">Orig. Invoice</th>
                                <th class="py-2.5 px-4">Date</th>
                                <th class="py-2.5 px-4 text-right">Refunded</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line">
                            @forelse ($purchaseReturns as $return)
                                <tr class="hover:bg-paper/50 transition-colors">
                                    <td class="py-2 px-4 font-mono text-xs">{{ $return->purchase->invoice_number ?? '—' }}</td>
                                    <td class="py-2 px-4 font-mono text-xs text-muted">{{ \Carbon\Carbon::parse($return->date)->format('d M, Y') }}</td>
                                    <td class="py-2 px-4 text-right font-mono text-xs font-semibold text-tag-red">Rs {{ number_format($return->received_amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="py-6 text-center text-muted">No purchase returns in this period.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
@endsection
