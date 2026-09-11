@extends('layouts.master')

@section('title', 'Supplier Ledger')

@section('content')
    <div class="space-y-6" x-data="supplierLedgerIndex()">

        <!-- Top Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-muted">Accounts &middot; Supplier</span>
                <h1 class="font-display text-2xl font-bold text-forest">{{ $supplier->name ?? '' }}</h1>
                <p class="text-xs font-mono text-muted mt-0.5">{{ $supplier->phone_number ?? 'No phone on file' }}</p>
            </div>
            <a href="{{ route('suppliers.index') }}"
                class="inline-flex items-center gap-2 rounded-[var(--radius-s)] border border-line bg-white px-3.5 py-2 text-xs font-semibold text-ink hover:bg-paper transition-colors">
                <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Suppliers
            </a>
        </div>
        <form method="GET" action="{{ route('suppliers.ledger.index') }}">

            <!-- Supplier Switcher + Date Range Filter -->
            <div class="panel bg-white">
                <div class="flex flex-col sm:flex-row sm:items-end gap-4">
                    <div class="flex-1 sm:max-w-xs">
                        <label for="supplier_select"
                            class="block text-xs font-semibold uppercase tracking-wider text-muted mb-1">
                            Supplier
                        </label>
                        <select name="supplier_id" x-model="supplierId" x-select2="supplierId"
                            placeholder="Search supplier..." class="w-full">
                            <option value="">Select Supplier</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}"
                                    {{ old('supplier_id', $purchase->supplier_id ?? '') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex items-end sm:flex-row sm:items-end gap-2 mt-4 pt-4 border-t border-line">
                    <div class="flex-1">
                        <label for="from_date" class="block text-xs font-semibold uppercase tracking-wider text-muted mb-1">
                            From Date
                        </label>
                        <input type="date" name="from_date" id="from_date" value="{{ $fromDate }}"
                            class="w-full rounded-[var(--radius-s)] border border-line bg-paper px-3.5 py-2 text-sm text-ink focus:border-forest focus:bg-white focus:outline-none focus:ring-1 focus:ring-forest transition-colors">
                    </div>
                    <div class="flex-1">
                        <label for="to_date" class="block text-xs font-semibold uppercase tracking-wider text-muted mb-1">
                            To Date
                        </label>
                        <input type="date" name="to_date" id="to_date" value="{{ $toDate }}"
                            class="w-full rounded-[var(--radius-s)] border border-line bg-paper px-3.5 py-2 text-sm text-ink focus:border-forest focus:bg-white focus:outline-none focus:ring-1 focus:ring-forest transition-colors">
                    </div>
                    <button type="submit"
                        class="rounded-[var(--radius-s)] bg-forest px-5 py-2 text-xs font-semibold text-white shadow-sm hover:bg-forest-light transition-all">
                        Apply
                    </button>
                    <a href="{{ route('suppliers.ledger.index', array_merge(request()->query(), ['export' => 'pdf'])) }}"
                        class="rounded-[var(--radius-s)] border border-line bg-white px-4 py-2 text-xs font-semibold text-muted hover:bg-paper transition-colors">
                        Export
                    </a>
                    @if (request()->filled('search'))
                        <a href="{{ route('suppliers.ledger.index') }}"
                            class="rounded-[var(--radius-s)] border border-line bg-white px-4 py-2 text-xs font-semibold text-muted hover:bg-paper transition-colors">
                            Reset
                        </a>
                    @endif
                </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="panel bg-white">
            <p class="text-xs font-semibold uppercase tracking-wider text-muted mb-1">Opening Balance</p>
            <p class="font-mono text-lg font-bold {{ $openingBalance >= 0 ? 'text-tag-red' : 'text-forest' }}">
                Rs {{ number_format(abs($openingBalance), 2) }}
                <span class="text-xs font-semibold">{{ $openingBalance >= 0 ? 'Cr' : 'Dr' }}</span>
            </p>
        </div>
        <div class="panel bg-white">
            <p class="text-xs font-semibold uppercase tracking-wider text-muted mb-1">Total Debit</p>
            <p class="font-mono text-lg font-bold text-forest">Rs {{ number_format($totalDebit, 2) }}</p>
        </div>
        <div class="panel bg-white">
            <p class="text-xs font-semibold uppercase tracking-wider text-muted mb-1">Total Credit</p>
            <p class="font-mono text-lg font-bold text-tag-red">Rs {{ number_format($totalCredit, 2) }}</p>
        </div>
        <div class="panel bg-white border-forest">
            <p class="text-xs font-semibold uppercase tracking-wider text-muted mb-1">Closing Balance</p>
            <p class="font-mono text-lg font-bold {{ $closingBalance >= 0 ? 'text-tag-red' : 'text-forest' }}">
                Rs {{ number_format(abs($closingBalance), 2) }}
                <span class="text-xs font-semibold">{{ $closingBalance >= 0 ? 'Cr' : 'Dr' }}</span>
            </p>
        </div>
    </div>

    <!-- Ledger Table -->
    {{-- @dd($supplier) --}}
    @if (filled(request('supplier_id')))
        <div class="panel bg-white p-0 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-ink">
                    <thead class="bg-paper border-b border-line text-xs font-semibold uppercase tracking-wider text-muted">
                        <tr>
                            <th class="py-3 px-4">Date</th>
                            <th class="py-3 px-4">Description</th>
                            <th class="py-3 px-4">Reference</th>
                            <th class="py-3 px-4 text-right">Debit</th>
                            <th class="py-3 px-4 text-right">Credit</th>
                            <th class="py-3 px-4 text-right">Balance</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        <!-- Opening Balance Row -->
                        <tr class="bg-paper/60">
                            <td class="py-3 px-4 font-mono text-xs text-muted">
                                {{ \Carbon\Carbon::parse($fromDate)->format('M d, Y') }}</td>
                            <td class="py-3 px-4 font-semibold text-ink" colspan="3">Opening Balance</td>
                            <td
                                class="py-3 px-4 text-right font-mono font-bold {{ $openingBalance >= 0 ? 'text-tag-red' : 'text-forest' }}">
                                Rs {{ number_format(abs($openingBalance), 2) }}
                                {{ $openingBalance >= 0 ? 'Cr' : 'Dr' }}
                            </td>
                        </tr>

                        @forelse ($entries as $entry)
                            <tr class="hover:bg-paper/50 transition-colors">
                                <td class="py-3 px-4 font-mono text-xs text-muted">
                                    {{ \Carbon\Carbon::parse($entry['date'])->format('M d, Y') }}
                                </td>
                                <td class="py-3 px-4 text-ink">{{ $entry['description'] }}</td>
                                <td class="py-3 px-4 font-mono text-xs text-muted">{{ $entry['reference'] ?? '—' }}
                                </td>
                                <td class="py-3 px-4 text-right font-mono text-xs font-semibold text-forest">
                                    {{ $entry['type'] === 'debit' ? 'Rs ' . number_format($entry['amount'], 2) : '—' }}
                                </td>
                                <td class="py-3 px-4 text-right font-mono text-xs font-semibold text-tag-red">
                                    {{ $entry['type'] === 'credit' ? 'Rs ' . number_format($entry['amount'], 2) : '—' }}
                                </td>
                                <td
                                    class="py-3 px-4 text-right font-mono text-xs font-bold {{ $entry['balance'] >= 0 ? 'text-tag-red' : 'text-forest' }}">
                                    Rs {{ number_format(abs($entry['balance']), 2) }}
                                    {{ $entry['balance'] >= 0 ? 'Cr' : 'Dr' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-muted">
                                    <p class="text-sm">No transactions in this date range.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="bg-forest-tint/40 border-t-2 border-forest">
                            <td class="py-3 px-4 font-bold text-forest" colspan="3">Totals &amp; Closing Balance</td>
                            <td class="py-3 px-4 text-right font-mono font-bold text-forest">
                                Rs {{ number_format($totalDebit, 2) }}
                            </td>
                            <td class="py-3 px-4 text-right font-mono font-bold text-tag-red">
                                Rs {{ number_format($totalCredit, 2) }}
                            </td>
                            <td
                                class="py-3 px-4 text-right font-mono font-bold {{ $closingBalance >= 0 ? 'text-tag-red' : 'text-forest' }}">
                                Rs {{ number_format(abs($closingBalance), 2) }}
                                {{ $closingBalance >= 0 ? 'Cr' : 'Dr' }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @endif

    <p class="text-[11px] text-muted">
        <strong>Cr</strong> = amount owed to the supplier (liability). <strong>Dr</strong> = credit in your favor /
        advance paid.
    </p>

    </div>
    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('supplierLedgerIndex', () => ({
                    supplierId: '{{ request('supplier_id') }}',

                }));
            });
        </script>
    @endpush
@endsection
