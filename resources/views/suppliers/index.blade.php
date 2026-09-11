@extends('layouts.master')

@section('title', 'Suppliers List')

@section('content')
    <div class="space-y-6">
        <x-toast />
        <!-- Top Action & Navigation Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-muted">Ledger Accounts</span>
                <h1 class="font-display text-2xl font-bold text-forest">Suppliers</h1>
            </div>
            <div class="flex items-center gap-2.5">
                {{-- <x-export-button route="suppliers.index" /> --}}

                <a href="{{ route('suppliers.create') }}"
                    class="inline-flex items-center gap-2 rounded-[var(--radius-s)] bg-forest px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-forest-light transition-all">
                    <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                     Add Supplier
                </a>
            </div>
        </div>

        <!-- Reusable Search Bar Component -->
        <x-search-bar :route="route('suppliers.index')" placeholder="Search suppliers by name or phone number..." />

        <!-- Suppliers Table -->
        <div class="panel bg-white p-0 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-ink">
                    <thead class="bg-paper border-b border-line text-xs font-semibold uppercase tracking-wider text-muted">
                        <tr>
                            <th class="py-3 px-4">Supplier Name</th>
                            <th class="py-3 px-4">Phone Number</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Opening Balance</th>
                            <th class="py-3 px-4 text-right">Current Payable</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @forelse($suppliers as $supplier)
                            <tr class="hover:bg-paper/50 transition-colors">
                                <!-- Supplier Name -->
                                <td class="py-3 px-4 font-medium text-ink">
                                    {{ $supplier->name }}
                                </td>

                                <!-- Phone Number -->
                                <td class="py-3 px-4 font-mono text-xs text-muted">
                                    {{ $supplier->phone_number ?? '—' }}
                                </td>
                                <!-- Phone Number -->
                                <td class="py-3 px-4 font-mono text-xs text-muted">
                                    <x-toggle-switch :id="$supplier->id" :active="$supplier->is_active" :route="route('suppliers.toggle_status', $supplier->id)" />
                                </td>

                                <!-- Opening Balance -->
                                <td class="py-3 px-4 text-right font-mono text-xs text-muted">
                                    Rs {{ number_format($supplier->opening_balance, 2) }}
                                </td>

                                <!-- Current Balance Status -->
                                <td class="py-3 px-4 text-right font-mono text-xs font-semibold">
                                    @if ($supplier->current_balance > 0)
                                        <span class="text-tag-red">Rs
                                            {{ number_format($supplier->current_balance, 2) }}</span>
                                    @else
                                        <span class="text-forest">Rs
                                            {{ number_format($supplier->current_balance, 2) }}</span>
                                    @endif
                                </td>

                                <!-- Actions -->
                                <td class="py-3 px-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('suppliers.edit', $supplier->id) }}"
                                            class="rounded-[var(--radius-s)] border border-line bg-white p-1.5 text-muted hover:text-forest hover:border-forest transition-colors"
                                            title="Edit Supplier">
                                            <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-muted">
                                    <p class="text-sm">No suppliers found matching your query.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Footer -->
            @if ($suppliers->hasPages())
                <div class="px-4 py-3 border-t border-line bg-paper">
                    {{ $suppliers->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
