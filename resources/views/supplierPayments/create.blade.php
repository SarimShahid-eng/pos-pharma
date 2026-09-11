@extends('layouts.master')

@section('title', 'Record Supplier Payment')

@section('content')
    <div x-data="supplierPaymentForm()" class="space-y-6">
        <x-toast />

        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-muted">Accounts Payable</span>
                <h1 class="font-display text-2xl font-bold text-forest">Record Supplier Payment</h1>
            </div>
            <a href="{{ route('supplierPayments.index') }}"
                class="inline-flex items-center gap-1.5 rounded-[var(--radius-s)] border border-line bg-white px-3.5 py-2 text-xs font-semibold text-muted hover:bg-paper transition-colors">
                &larr; Back to List
            </a>
        </div>

        <form action="{{ route('supplierPayments.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Payment Setup Card -->
            <div class="panel bg-white space-y-4">
                <h2 class="text-xs font-bold uppercase tracking-wider text-forest border-b border-line pb-2">
                    Payment Details
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <input type="hidden" name="update_id" value="{{ @$supplierPayment->update_id }}">
                    <!-- Supplier Selection (Requested Select Component) -->
                    <div class="space-y-1">
                        <label class="block text-xs font-semibold text-ink">
                            Supplier <span class="text-tag-red">*</span>
                        </label>
                        <select name="supplier_id" x-model="supplierId" x-select2="supplierId" required
                            placeholder="Search supplier..." class="w-full">
                            <option value="">Select Supplier</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}"
                                    {{ old('supplier_id', $supplierPayment->supplier_id ?? '') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('supplier_id')
                            <p class="mt-1 text-xs text-tag-red font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Payment Date -->
                    <div>
                        <label for="date" class="block text-xs font-semibold uppercase tracking-wider text-muted mb-1">
                            Date <span class="text-tag-red">*</span>
                        </label>
                        <input type="date" name="date" id="date" value="{{ old('date', date('Y-m-d')) }}"
                            required
                            class="w-full rounded-[var(--radius-s)] border @error('date') border-tag-red @else border-line @enderror bg-paper px-3.5 py-2 text-sm text-ink focus:border-forest focus:bg-white focus:outline-none focus:ring-1 focus:ring-forest transition-colors">
                        @error('date')
                            <p class="mt-1 text-xs text-tag-red font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Payment Type (Debit / Credit) -->
                    <div>
                        <label for="payment_method"
                            class="block text-xs font-semibold uppercase tracking-wider text-muted mb-1">
                            Payment Method <span class="text-tag-red">*</span>
                        </label>
                        <select name="payment_method" id="payment_method" x-model="payment_method" required
                            class="w-full rounded-[var(--radius-s)] border @error('payment_method') border-tag-red @else border-line @enderror bg-paper px-3.5 py-2 text-sm text-ink focus:border-forest focus:bg-white focus:outline-none focus:ring-1 focus:ring-forest transition-colors">
                            <option value="cash" {{ old('payment_method') === 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="bank" {{ old('payment_method') === 'bank' ? 'selected' : '' }}>Bank</option>
                        </select>
                        <p class="mt-1 text-[11px] text-muted">
                            <span x-show="payment_method === 'cash'">Will be paid through cash.</span>
                            <span x-show="payment_method === 'bank'">Will be paid through bank</span>
                        </p>
                        @error('payment_method')
                            <p class="mt-1 text-xs text-tag-red font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Amount -->
                    <div>
                        <label for="amount" class="block text-xs font-semibold uppercase tracking-wider text-muted mb-1">
                            Amount (Rs) <span class="text-tag-red">*</span>
                        </label>
                        <input type="number" step="0.01" min="0.01" name="amount" id="amount"
                            x-model.number="amount" placeholder="0.00" required
                            class="w-full rounded-[var(--radius-s)] border @error('amount') border-tag-red @else border-line @enderror bg-paper px-3.5 py-2 text-sm font-mono text-ink focus:border-forest focus:bg-white focus:outline-none focus:ring-1 focus:ring-forest transition-colors">
                        @error('amount')
                            <p class="mt-1 text-xs text-tag-red font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Notes / Remarks -->
                <div>
                    <label for="notes" class="block text-xs font-semibold uppercase tracking-wider text-muted mb-1">
                        Notes / Remarks
                    </label>
                    <textarea name="notes" id="notes" rows="3" placeholder="e.g. Paid via Bank Transfer (Ref #10293)"
                        class="w-full rounded-[var(--radius-s)] border border-line bg-paper p-3 text-xs text-ink focus:border-forest focus:bg-white focus:outline-none focus:ring-1 focus:ring-forest transition-colors">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-xs text-tag-red font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Form Action Controls -->
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('supplierPayments.index') }}"
                    class="rounded-[var(--radius-s)] border border-line bg-white px-4 py-2.5 text-xs font-semibold text-muted hover:bg-paper transition-colors">
                    Cancel
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-[var(--radius-s)] bg-forest px-6 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-forest-light transition-all cursor-pointer">
                    <span>Save Payment</span>
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('supplierPaymentForm', () => ({
                supplierId: '{{ old('supplier_id', '') }}',
                type: '{{ old('type', 'debit') }}',
                amount: {{ old('amount', 0) }}
            }));
        });
    </script>
@endsection
