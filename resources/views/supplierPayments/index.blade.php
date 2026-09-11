@extends('layouts.master')

@section('title', 'Supplier Payments')

@section('content')

    <div x-data="supplierPaymentIndex()" class="space-y-6">
        <x-toast />
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-muted">Payments</span>
                <h1 class="font-display text-2xl font-bold text-forest">Suppliers Payments</h1>
            </div>
            <div class="flex items-center gap-2.5">
                {{-- <x-export-button route="suppliers.index" /> --}}

                <a href="{{ route('supplierPayments.create') }}"
                    class="inline-flex items-center gap-2 rounded-[var(--radius-s)] bg-forest px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-forest-light transition-all">
                    <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Supplier Payment
                </a>
            </div>
        </div>

        <!-- Top Action Bar -->
        <x-search-bar-filter :route="route('supplierPayments.index')" placeholder="Search by name or code...">

            {{-- Slot for Select2 Dropdowns / Filters --}}
            <x-slot:filters>
                <div class="w-full sm:w-48">
                    <select name="supplier_id" x-model="supplierId" x-select2="supplierId" placeholder="Search supplier..."
                        class="w-full">
                        <option value="">Select Supplier</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}"
                                {{ old('supplier_id', $purchase->supplier_id ?? '') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <input type="date" name="date" id="date" value="{{ request('date', date('Y-m-d')) }}"
                        required
                        class="w-full rounded-[var(--radius-s)] border  bg-paper px-3.5 py-2 text-sm text-ink focus:border-forest focus:bg-white focus:outline-none focus:ring-1 focus:ring-forest transition-colors">
                    @error('date')
                        <p class="mt-1 text-xs text-tag-red font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </x-slot:filters>

            {{-- Slot for Action Buttons (Export, Print, Add New) --}}
            {{-- <x-slot:actions>
                <a href="#"
                    class="inline-flex items-center gap-2 rounded-[var(--radius-s)] border border-line bg-white px-3 py-2 text-xs font-semibold text-ink hover:bg-paper transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export CSV
                </a>
            </x-slot:actions> --}}

        </x-search-bar-filter>


        <!-- Payments Table -->
        <div class="panel bg-white p-0 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-ink">
                    <thead class="bg-paper border-b border-line text-xs font-semibold uppercase tracking-wider text-muted">
                        <tr>
                            <th class="py-3 px-4">Voucher #</th>
                            <th class="py-3 px-4">Supplier</th>
                            <th class="py-3 px-4">Date</th>
                            <th class="py-3 px-4">Method</th>
                            {{-- <th class="py-3 px-4 text-center">Type</th> --}}
                            <th class="py-3 px-4 text-right">Amount</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @forelse($supplierPayments as $payment)
                            <tr class="hover:bg-paper/50 transition-colors">
                                <!-- Voucher Number -->
                                <td class="py-3 px-4 font-mono text-xs font-bold text-forest">
                                    {{ $payment->voucher_no }}
                                </td>

                                <!-- Supplier Name -->
                                <td class="py-3 px-4 font-medium text-ink">
                                    {{ $payment->supplier->name ?? 'N/A' }}
                                </td>

                                <!-- Payment Date -->
                                <td class="py-3 px-4 font-mono text-xs text-muted">
                                    {{ \Carbon\Carbon::parse($payment->date)->format('M d, Y') }}
                                </td>

                                <!-- Type Badge (Credit / Debit) -->
                                <td class="py-3 px-4 text-center">
                                    @if ($payment->payment_method === 'cash')
                                        <span
                                            class="inline-flex items-center rounded-full bg-forest-tint/60 px-2.5 py-0.5 text-[11px] font-bold text-forest uppercase tracking-wider">
                                            Cash
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center rounded-full bg-tag-red/10 px-2.5 py-0.5 text-[11px] font-bold text-tag-red uppercase tracking-wider">
                                            Bank
                                        </span>
                                    @endif
                                </td>

                                <!-- Amount -->
                                <td
                                    class="py-3 px-4 text-right font-mono text-xs font-bold {{ $payment->type === 'debit' ? 'text-forest' : 'text-tag-red' }}">
                                    Rs {{ number_format($payment->amount, 2) }}
                                </td>

                                <!-- Actions -->
                                <td class="py-3 px-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">
                                        <button type="button"
                                            @click="openModal({{ json_encode($payment->load('supplier')) }})"
                                            class="rounded-[var(--radius-s)] border border-line bg-white p-1.5 text-muted hover:text-forest hover:border-forest transition-colors"
                                            title="View Payment Voucher">
                                            <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-muted">
                                    <p class="text-sm">No supplier payment records found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Footer -->
            @if ($supplierPayments->hasPages())
                <div class="px-4 py-3 border-t border-line bg-paper">
                    {{ $supplierPayments->withQueryString()->links() }}
                </div>
            @endif
        </div>
        @include('supplierPayments.modal')

    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('supplierPaymentIndex', () => ({
                showModal: false,
                activePayment: null,
                supplierId: '{{ request('supplier_id') }}',

                openModal(payment) {
                    this.activePayment = payment;
                    this.showModal = true;
                },

                closeModal() {
                    this.showModal = false;
                    this.activePayment = null;
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
