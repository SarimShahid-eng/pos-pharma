@extends('layouts.master')

@section('title', isset($product) ? 'Edit Product' : 'Add New Product')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        {{-- @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif --}}
        <!-- Header & Action Bar -->
        <div class="flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-muted">Catalog Management</span>
                <h1 class="font-display text-2xl font-bold text-forest">
                    {{ isset($product) ? 'Edit Product: ' . $product->name : 'Add New Product' }}
                </h1>
            </div>
            <a href="{{ route('products.index') }}"
                class="inline-flex items-center gap-2 rounded-[var(--radius-s)] border border-line bg-white px-3.5 py-2 text-xs font-semibold text-ink hover:bg-paper transition-colors">
                <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Products
            </a>
        </div>

        <!-- Main Product Form -->
        <form action="{{ route('products.store') }}" method="POST" x-data="productForm({
            cost: {{ old('cost_price', $product->cost_price ?? 0) }},
            sale: {{ old('sale_price', $product->sale_price ?? 0) }},
            discount: {{ old('discount', $product->discount ?? 0) }}
        })"
            class="panel bg-white space-y-6">
            @csrf
            <input type="hidden" name="update_id" value="{{ @$product->id }}">


            <!-- Section 1: Identification & Scanning -->
            <div class="space-y-4">
                <h2 class="text-xs font-bold uppercase tracking-wider text-forest border-b border-line pb-2">
                    1. Identification & Labeling
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Barcode Number -->
                    <div>
                        <label for="barcode" class="block text-xs font-semibold uppercase tracking-wider text-muted mb-1">
                            Product Code <span class="text-tag-red">*</span>
                        </label>
                        <div class="relative flex gap-2">
                            <div class="relative flex-1">
                                <input type="text" name="barcode" id="barcode" x-model="barcodeValue" required
                                    placeholder="e.g. 8901234567890"
                                    class="w-full rounded-[var(--radius-s)] border @error('barcode') border-tag-red @else border-line @enderror bg-paper px-3.5 py-2 text-sm font-mono text-ink focus:border-forest focus:bg-white focus:outline-none focus:ring-1 focus:ring-forest transition-colors">
                            </div>

                            <!-- Auto Generate Button -->
                            <button type="button" @click="generateBarcode()" :disabled="isGenerating"
                                class="inline-flex items-center gap-1.5 rounded-[var(--radius-s)] border border-line bg-paper px-3 py-2 text-xs font-semibold text-forest hover:bg-forest-tint transition-colors cursor-pointer disabled:opacity-50">
                                <svg class="w-4 h-4 stroke-current fill-none" :class="{ 'animate-spin': isGenerating }"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                <span x-text="isGenerating ? 'Generating...' : 'Auto'"></span>
                            </button>
                        </div>
                        @error('barcode')
                            <p class="mt-1 text-xs text-tag-red font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Custom Barcode Sticker Title -->
                    <div>
                        <label for="label_title"
                            class="block text-xs font-semibold uppercase tracking-wider text-muted mb-1">
                            Sticker Label Title
                        </label>
                        <input type="text" name="label_title" id="label_title"
                            value="{{ old('label_title', $product->label_title ?? '') }}" placeholder="e.g. FRESHMART - 1KG"
                            class="w-full rounded-[var(--radius-s)] border @error('label_title') border-tag-red @else border-line @enderror bg-paper px-3.5 py-2 text-sm text-ink focus:border-forest focus:bg-white focus:outline-none focus:ring-1 focus:ring-forest transition-colors">
                        @error('label_title')
                            <p class="mt-1 text-xs text-tag-red font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Product Full Name -->
                <div>
                    <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-muted mb-1">
                        Product Name <span class="text-tag-red">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name', $product->name ?? '') }}"
                        required placeholder="e.g. Basmati Rice Premium 5kg"
                        class="w-full rounded-[var(--radius-s)] border @error('name') border-tag-red @else border-line @enderror bg-paper px-3.5 py-2 text-sm text-ink focus:border-forest focus:bg-white focus:outline-none focus:ring-1 focus:ring-forest transition-colors">
                    @error('name')
                        <p class="mt-1 text-xs text-tag-red font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Section 2: Pricing & Profit Mechanics -->
            <div class="space-y-4">
                <h2 class="text-xs font-bold uppercase tracking-wider text-forest border-b border-line pb-2">
                    2. Pricing & Margin Calculation
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                    <!-- Cost Price -->
                    <div>
                        <label for="cost_price"
                            class="block text-xs font-semibold uppercase tracking-wider text-muted mb-1">
                            Cost Price <span class="text-tag-red">*</span>
                        </label>
                        <input type="number" step="0.01" name="cost_price" id="cost_price" x-model.number="cost"
                            value="{{ old('cost_price', $product->cost_price ?? '0.00') }}" required
                            class="w-full rounded-[var(--radius-s)] border @error('cost_price') border-tag-red @else border-line @enderror bg-paper px-3.5 py-2 text-sm font-mono text-ink focus:border-forest focus:bg-white focus:outline-none focus:ring-1 focus:ring-forest transition-colors">
                        @error('cost_price')
                            <p class="mt-1 text-xs text-tag-red font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Retail Sale Price -->
                    <div>
                        <label for="sale_price"
                            class="block text-xs font-semibold uppercase tracking-wider text-muted mb-1">
                            Sale Price <span class="text-tag-red">*</span>
                        </label>
                        <input type="number" step="0.01" name="sale_price" id="sale_price" x-model.number="sale"
                            value="{{ old('sale_price', $product->sale_price ?? '0.00') }}" required
                            class="w-full rounded-[var(--radius-s)] border @error('sale_price') border-tag-red @else border-line @enderror bg-paper px-3.5 py-2 text-sm font-mono text-ink focus:border-forest focus:bg-white focus:outline-none focus:ring-1 focus:ring-forest transition-colors">
                        @error('sale_price')
                            <p class="mt-1 text-xs text-tag-red font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Unit Discount -->
                    <div>
                        <label for="discount" class="block text-xs font-semibold uppercase tracking-wider text-muted mb-1">
                            Discount (Flat)
                        </label>
                        <input type="number" step="0.01" name="discount" id="discount" x-model.number="discount"
                            value="{{ old('discount', $product->discount ?? '0.00') }}"
                            class="w-full rounded-[var(--radius-s)] border @error('discount') border-tag-red @else border-line @enderror bg-paper px-3.5 py-2 text-sm font-mono text-ink focus:border-forest focus:bg-white focus:outline-none focus:ring-1 focus:ring-forest transition-colors">
                        @error('discount')
                            <p class="mt-1 text-xs text-tag-red font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Auto-Calculated Profit Margin -->
                    <div>
                        <label for="profit" class="block text-xs font-semibold uppercase tracking-wider text-muted mb-1">
                            Estimated Profit
                        </label>
                        <input type="number" step="0.01" name="profit" id="profit" :value="calculateProfit()"
                            readonly
                            class="w-full rounded-[var(--radius-s)] border border-line bg-forest-tint px-3.5 py-2 text-sm font-mono font-semibold text-forest cursor-not-allowed">
                        @error('profit')
                            <p class="mt-1 text-xs text-tag-red font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Section 3: Stock Setup & Measurement -->
            <div class="space-y-4">
                <h2 class="text-xs font-bold uppercase tracking-wider text-forest border-b border-line pb-2">
                    3. Inventory & Measurement
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Unit Type -->
                    <div>
                        <label for="unit"
                            class="block text-xs font-semibold uppercase tracking-wider text-muted mb-1">
                            Unit Type
                        </label>
                        <select name="unit" id="unit"
                            class="w-full rounded-[var(--radius-s)] border @error('unit') border-tag-red @else border-line @enderror bg-paper px-3.5 py-2 text-sm text-ink focus:border-forest focus:bg-white focus:outline-none focus:ring-1 focus:ring-forest transition-colors">
                            <option value="pcs" {{ old('unit', $product->unit ?? '') == 'pcs' ? 'selected' : '' }}>
                                Pieces (pcs)</option>
                            <option value="pack" {{ old('unit', $product->unit ?? '') == 'pack' ? 'selected' : '' }}>
                                Pack</option>
                            <option value="kg" {{ old('unit', $product->unit ?? '') == 'kg' ? 'selected' : '' }}>
                                Kilogram (kg)</option>
                            <option value="ltr" {{ old('unit', $product->unit ?? '') == 'ltr' ? 'selected' : '' }}>
                                Liter (ltr)</option>
                        </select>
                        @error('unit')
                            <p class="mt-1 text-xs text-tag-red font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Opening/Current Stock -->
                    <div>
                        <label for="stock_qty"
                            class="block text-xs font-semibold uppercase tracking-wider text-muted mb-1">
                            Stock Quantity <span class="text-xxs">(how much you already have)</span> <span class="text-tag-red">*</span>
                        </label>
                        <input type="number" step="0.01" name="stock_qty" id="stock_qty"
                            value="{{ old('stock_qty', $product->stock_qty ?? '0') }}" required
                            class="w-full rounded-[var(--radius-s)] border @error('stock_qty') border-tag-red @else border-line @enderror bg-paper px-3.5 py-2 text-sm font-mono text-ink focus:border-forest focus:bg-white focus:outline-none focus:ring-1 focus:ring-forest transition-colors">
                        @error('stock_qty')
                            <p class="mt-1 text-xs text-tag-red font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Form Submission Footer -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-line">
                <a href="{{ route('products.index') }}"
                    class="rounded-[var(--radius-s)] border border-line bg-white px-4 py-2.5 text-xs font-semibold text-muted hover:bg-paper transition-colors">
                    Cancel
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-[var(--radius-s)] bg-forest px-5 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-forest-light transition-all cursor-pointer">
                    <svg class="w-4 h-4 fill-none stroke-current" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ isset($product) ? 'Update Product' : 'Save Product' }}</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Reactive Profit Calculation Script -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('productForm', (initials) => ({
                barcodeValue: @json(old('barcode', $product->barcode ?? '')),
                cost: initials.cost || 0,
                sale: initials.sale || 0,
                isGenerating: false,
                discount: initials.discount || 0,
                async generateBarcode() {
                    this.isGenerating = true;
                    try {
                        let response = await fetch("{{ route('products.generate_barcode') }}");
                        let data = await response.json();
                        if (data.barcode) {
                            this.barcodeValue = data.barcode;
                        }
                    } catch (error) {
                        console.error('Failed to generate barcode', error);
                    } finally {
                        this.isGenerating = false;
                    }
                },
                calculateProfit() {
                    let netSale = (parseFloat(this.sale) || 0) - (parseFloat(this.discount) || 0);
                    let calculated = netSale - (parseFloat(this.cost) || 0);
                    return calculated.toFixed(2);
                }
            }))
        });
    </script>
@endsection
