@extends('layouts.master')

@section('title', isset($supplier) ? 'Edit Supplier' : 'Add New Supplier')

@section('content')
    <div class="max-w-2xl mx-auto space-y-6">

        <!-- Header Bar -->
        <div class="flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-muted">Supplier Accounts</span>
                <h1 class="font-display text-2xl font-bold text-forest">
                    {{ isset($supplier) ? 'Edit Supplier: ' . $supplier->name : 'Add New Supplier' }}
                </h1>
            </div>
            <a href="{{ route('suppliers.index') }}"
                class="inline-flex items-center gap-2 rounded-[var(--radius-s)] border border-line bg-white px-3.5 py-2 text-xs font-semibold text-ink hover:bg-paper transition-colors">
                <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Suppliers
            </a>
        </div>

        <!-- Main Supplier Form -->
        <form action="{{ route('suppliers.store') }}" method="POST" class="panel bg-white space-y-5">
            @csrf
            {{-- @if (isset($supplier))
                @method('PUT')
            @endif --}}
            <input type="hidden" name="update_id" value="{{ @$supplier->id }}">

            <div class="space-y-4">
                <h2 class="text-xs font-bold uppercase tracking-wider text-forest border-b border-line pb-2">
                    Contact & General Info
                </h2>

                <!-- Entry Date -->
                <div>
                    <label for="date" class="block text-xs font-semibold uppercase tracking-wider text-muted mb-1">
                        Date <span class="text-tag-red">*</span>
                    </label>
                    <input type="date" name="date" id="date"
                        value="{{ old('date', isset($supplier->date) ? \Carbon\Carbon::parse($supplier->date)->format('Y-m-d') : date('Y-m-d')) }}"
                        required
                        class="w-full rounded-[var(--radius-s)] border @error('date') border-tag-red @else border-line @enderror bg-paper px-3.5 py-2 text-sm font-mono text-ink focus:border-forest focus:bg-white focus:outline-none focus:ring-1 focus:ring-forest transition-colors">
                    @error('date')
                        <p class="mt-1 text-xs text-tag-red font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Supplier Name -->
                <div>
                    <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-muted mb-1">
                        Supplier / Vendor Name <span class="text-tag-red">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name', $supplier->name ?? '') }}"
                        required placeholder="e.g. Metro Traders Pvt Ltd"
                        class="w-full rounded-[var(--radius-s)] border @error('name') border-tag-red @else border-line @enderror bg-paper px-3.5 py-2 text-sm text-ink focus:border-forest focus:bg-white focus:outline-none focus:ring-1 focus:ring-forest transition-colors">
                    @error('name')
                        <p class="mt-1 text-xs text-tag-red font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone Number -->
                <div>
                    <label for="phone_number" class="block text-xs font-semibold uppercase tracking-wider text-muted mb-1">
                        Phone Number
                    </label>
                    <input type="text" name="phone_number" id="phone_number"
                        value="{{ old('phone_number', $supplier->phone_number ?? '') }}" placeholder="e.g. 03001234567"
                        class="w-full rounded-[var(--radius-s)] border @error('phone_number') border-tag-red @else border-line @enderror bg-paper px-3.5 py-2 text-sm font-mono text-ink focus:border-forest focus:bg-white focus:outline-none focus:ring-1 focus:ring-forest transition-colors">
                    @error('phone_number')
                        <p class="mt-1 text-xs text-tag-red font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="space-y-4 pt-2">
                <h2 class="text-xs font-bold uppercase tracking-wider text-forest border-b border-line pb-2">
                    Ledger Initialization
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Opening Balance -->
                    <div>
                        <label for="opening_balance"
                            class="block text-xs font-semibold uppercase tracking-wider text-muted mb-1">
                            Opening Balance (Payable)
                        </label>
                        <input type="number" step="0.01" name="opening_balance" id="opening_balance"
                            value="{{ old('opening_balance', $supplier->opening_balance ?? '0.00') }}"
                            {{ isset($supplier) ? 'readonly' : '' }}
                            class="w-full rounded-[var(--radius-s)] border @error('opening_balance') border-tag-red @else border-line @enderror {{ isset($supplier) ? 'bg-forest-tint/50 text-muted cursor-not-allowed' : 'bg-paper' }} px-3.5 py-2 text-sm font-mono text-ink focus:border-forest focus:bg-white focus:outline-none focus:ring-1 focus:ring-forest transition-colors">
                        <p class="mt-1 text-[11px] text-muted">Initial outstanding amount when starting system.</p>
                        @error('opening_balance')
                            <p class="mt-1 text-xs text-tag-red font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Current Balance Display -->
                    @if (isset($supplier))
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-muted mb-1">
                                Current Balance
                            </label>
                            <input type="text" value="Rs {{ number_format($supplier->current_balance, 2) }}" readonly
                                class="w-full rounded-[var(--radius-s)] border border-line bg-forest-tint px-3.5 py-2 text-sm font-mono font-semibold text-forest cursor-not-allowed">
                            <p class="mt-1 text-[11px] text-muted">Auto-calculated from ledger purchases and payments.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-line">
                <a href="{{ route('suppliers.index') }}"
                    class="rounded-[var(--radius-s)] border border-line bg-white px-4 py-2.5 text-xs font-semibold text-muted hover:bg-paper transition-colors">
                    Cancel
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-[var(--radius-s)] bg-forest px-5 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-forest-light transition-all cursor-pointer">
                    <svg class="w-4 h-4 fill-none stroke-current" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ isset($supplier) ? 'Update Supplier' : 'Save Supplier' }}</span>
                </button>
            </div>
        </form>
    </div>
@endsection
