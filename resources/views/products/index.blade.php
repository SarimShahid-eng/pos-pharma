@extends('layouts.master')

@section('title', 'Product Catalog')

@section('content')

    <div class="space-y-6">
        <x-toast />
        <!-- Top Action & Navigation Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-muted">Catalog & Stock</span>
                <h1 class="font-display text-2xl font-bold text-forest">Products</h1>
            </div>
            <div class="flex items-center gap-2.5">
                <!-- Export Button (href left blank for route integration) -->
                {{-- <x-export-button /> --}}

                <!-- Create Product Link -->
                <a href="{{ route('products.create') }}"
                    class="inline-flex items-center gap-2 rounded-[var(--radius-s)] bg-forest px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-forest-light transition-all">
                    <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Product
                </a>
            </div>
        </div>

        <!-- Search & Filter Controls -->
        <!-- Top Action Bar -->
        <x-search-bar-filter :route="route('products.index')" placeholder="Search products by barcode, title, or label name...">
            {{-- Slot for Action Buttons (Export, Print, Add New) --}}
            <x-slot:actions>
                <a href="{{ route('products.index', array_merge(request()->query(), ['export' => 'csv'])) }}"
                    class="inline-flex items-center gap-2 rounded-[var(--radius-s)] border border-line bg-white px-3 py-2 text-xs font-semibold text-ink hover:bg-paper transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export CSV
                </a>
            </x-slot:actions>

        </x-search-bar-filter>
        <!-- Products Data Table -->
        <div class="panel bg-white p-0 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-ink">
                    <thead class="bg-paper border-b border-line text-xs font-semibold uppercase tracking-wider text-muted">
                        <tr>
                            <th class="py-3 px-4">Product code / Title</th>
                            <th class="py-3 px-4">Product Name</th>
                            <th class="py-3 px-4 text-right">Cost Price</th>
                            <th class="py-3 px-4 text-right">Sale Price</th>
                            <th class="py-3 px-4 text-right">Profit</th>
                            <th class="py-3 px-4 text-center">Stock</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @forelse($products as $product)
                            <tr class="hover:bg-paper/50 transition-colors">
                                <!-- Barcode / Sticker Label -->
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <div class="font-mono text-xs font-semibold text-forest">{{ $product->barcode }}</div>
                                    @if ($product->label_title)
                                        <div class="text-[11px] text-muted">{{ $product->label_title }}</div>
                                    @endif
                                </td>

                                <!-- Product Name & Unit -->
                                <td class="py-3 px-4">
                                    <div class="font-medium text-ink">{{ $product->name }}</div>
                                    <span class="text-[11px] text-muted uppercase">{{ $product->unit }}</span>
                                </td>

                                <!-- Cost Price -->
                                <td class="py-3 px-4 text-right font-mono text-xs">
                                    Rs {{ number_format($product->cost_price, 2) }}
                                </td>

                                <!-- Sale Price & Discount -->
                                <td class="py-3 px-4 text-right font-mono text-xs font-semibold text-forest">
                                    Rs {{ number_format($product->sale_price, 2) }}
                                    @if ($product->discount > 0)
                                        <span class="block text-[10px] text-tag-red font-normal">-Rs
                                            {{ number_format($product->discount, 2) }} off</span>
                                    @endif
                                </td>

                                <!-- Profit -->
                                <td class="py-3 px-4 text-right font-mono text-xs text-forest">
                                    Rs {{ number_format($product->profit, 2) }}
                                </td>

                                <!-- Stock Quantity Status -->
                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                    @if ($product->stock_qty <= 5)
                                        <span class="pill pill-warn">{{ number_format($product->stock_quantity, 2) }}
                                            {{ $product->unit }} left</span>
                                    @else
                                        <span class="pill pill-completed">{{ number_format($product->stock_quantity, 2) }}
                                            {{ $product->unit }}</span>
                                    @endif
                                </td>

                                <!-- Actions -->
                                <td class="py-3 px-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('products.edit', $product->id) }}"
                                            class="rounded-[var(--radius-s)] border border-line bg-white p-1.5 text-muted hover:text-forest hover:border-forest transition-colors"
                                            title="Edit Product">
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
                                <td colspan="7" class="py-8 text-center text-muted">
                                    <p class="text-sm">No products found matching your search.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Footer -->
            @if ($products->hasPages())
                <div class="px-4 py-3 border-t border-line bg-paper">
                    {{ $products->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
