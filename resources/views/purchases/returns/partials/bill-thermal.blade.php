@php
    $shopName = config('app.shop_name', 'Tawakkal Mart');
    $shopAddress = config('app.shop_address', 'Main Bazaar Road, Hyderabad');
    $shopPhone = config('app.shop_phone', '0300-1234567');

    $originalPurchase = $purchaseReturn->purchase;
    $invoiceNumber = $originalPurchase->invoice_number ?? 'N/A';
    $supplier = $originalPurchase->supplier ?? null;

    $returnDate = $purchaseReturn->formatted_date ? $purchase->formatted_date : now()->format('d-M-Y h:i A');

    $originalGross = $originalPurchase
        ? (float) $originalPurchase->total_amount + (float) $originalPurchase->discount_amount
        : 0;
@endphp

<style>
    #bill-thermal-wrap .receipt {
        width: 80mm;
        margin: 0 auto;
        background: #fff;
        color: #000;
        font-family: 'IBM Plex Mono', 'Courier New', monospace;
        font-size: 11px;
        line-height: 1.45;
        padding: 4mm;
        border: 1px solid #ddd;
    }

    #bill-thermal-wrap .receipt-divider {
        border-top: 1px dashed #000;
        margin: 6px 0;
    }

    @media print {
        #bill-thermal-wrap .receipt {
            border: none;
            width: 80mm;
        }

        @page {
            size: 80mm auto;
            margin: 0;
        }
    }
</style>

<div id="bill-thermal-wrap">
    <div class="receipt">

        <!-- Shop Header -->
        <div class="text-center">
            <p class="text-sm font-bold uppercase tracking-wide">{{ $shopName }}</p>
            <p class="text-[10px]">{{ $shopAddress }}</p>
            <p class="text-[10px]">Tel: {{ $shopPhone }}</p>
            <p class="text-xs font-bold uppercase tracking-wider mt-1 border-y border-black py-0.5">*** PURCHASE RETURN
                ***</p>
        </div>

        <div class="receipt-divider"></div>

        <!-- Meta Data -->
        <div class="flex justify-between"><span>Orig. Invoice #</span><span class="font-bold">{{ $invoiceNumber }}</span>
        </div>
        @if ($originalPurchase->reference_number ?? false)
            <div class="flex justify-between"><span>Supplier Ref #</span><span
                    class="font-mono">{{ $originalPurchase->reference_number }}</span></div>
        @endif
        <div class="flex justify-between"><span>Return Date</span><span>{{ $returnDate }}</span></div>

        @if ($supplier)
            <div class="flex justify-between"><span>Supplier</span><span class="font-bold">{{ $supplier->name }}</span>
            </div>
            @if ($supplier->phone_number)
                <div class="flex justify-between"><span>Phone</span><span>{{ $supplier->phone_number }}</span></div>
            @endif
        @endif

        <div class="receipt-divider"></div>

        <!-- Returned Items Breakdown -->
        <div class="flex justify-between text-[10px] font-bold border-b border-black pb-1 mb-1">
            <span>ITEM / QTY BREAKDOWN</span>
            <span>RETURN VALUE</span>
        </div>

        @foreach ($purchaseReturn->purchaseReturnItems as $item)
            @php
                $originalPurchaseItem = $item->purchaseItem;
                $originalQty = (float) ($originalPurchaseItem->qty ?? 0);
                $returnedQty = (float) $item->qty;

                // Live remaining across ALL returns against this line item,
                // not just this one event — accurate even after multiple
                // separate returns on the same purchase.
                $remainingNow = $originalPurchaseItem ? $originalPurchaseItem->max_returnable_qty : null;
            @endphp

            <div class="mb-2">
                <!-- Product Name -->
                <div class="font-bold">
                    {{ $item->product->name ?? 'Deleted Product' }}
                </div>

                <!-- Quantity Tracking -->
                <div class="text-[10px] pl-1 text-black/90 mb-1">
                    @if ($originalQty > 0)
                        <div class="flex justify-between">
                            <span>• Orig. Purchased Qty:</span>
                            <span>{{ rtrim(rtrim(number_format($originalQty, 2), '0'), '.') }}</span>
                        </div>
                    @endif

                    <div class="flex justify-between font-semibold">
                        <span>• Returned Qty:</span>
                        <span>-{{ rtrim(rtrim(number_format($returnedQty, 2), '0'), '.') }}</span>
                    </div>

                    @if (!is_null($remainingNow))
                        <div class="flex justify-between text-[9px] text-black/70">
                            <span>• Available Now:</span>
                            <span>{{ rtrim(rtrim(number_format($remainingNow, 2), '0'), '.') }}</span>
                        </div>
                    @endif
                </div>

                <!-- Rate x Qty = Amount (no per-line discount on purchase returns) -->
                <div class="flex justify-between text-[10px] font-bold border-t border-dotted border-black/30 pt-0.5">
                    <span>{{ rtrim(rtrim(number_format($returnedQty, 2), '0'), '.') }} x Rs
                        {{ number_format($item->unit_cost, 2) }}</span>
                    <span>Rs {{ number_format($item->amount, 2) }}</span>
                </div>
            </div>
        @endforeach

        <!-- Original Purchase Financial Reference -->
        @if ($originalPurchase)
            <div class="receipt-divider"></div>
            <div class="text-[9px] text-black/80 space-y-0.5 bg-black/5 p-1 rounded">
                <div class="font-bold text-[10px] uppercase border-b border-black/20 pb-0.5 mb-0.5">Original Purchase
                    Reference</div>
                <div class="flex justify-between">
                    <span>Orig. Gross Total:</span>
                    <span>Rs {{ number_format($originalGross, 2) }}</span>
                </div>
                @if (($originalPurchase->discount_amount ?? 0) > 0)
                    <div class="flex justify-between">
                        <span>Orig. Total Discount:</span>
                        <span>-Rs {{ number_format($originalPurchase->discount_amount, 2) }}</span>
                    </div>
                @endif
                <div class="flex justify-between font-bold border-t border-dotted border-black/20 pt-0.5">
                    <span>Orig. Net Payable:</span>
                    <span>Rs {{ number_format($originalPurchase->total_amount, 2) }}</span>
                </div>
            </div>
        @endif

        <div class="receipt-divider"></div>

        <!-- Return Financial Summary -->
        <div class="space-y-0.5">
            @if ($purchaseReturn->total_amount > $purchaseReturn->received_amount)
                <div class="flex justify-between text-[10px]">
                    <span>Returned Value</span>
                    <span>Rs {{ number_format($purchaseReturn->total_amount, 2) }}</span>
                </div>

                <div class="flex justify-between text-[10px] text-black">
                    <span>Retained as Store Credit</span>
                    <span>-Rs
                        {{ number_format($purchaseReturn->total_amount - $purchaseReturn->received_amount, 2) }}</span>
                </div>

                <div class="flex justify-between text-xs font-bold border-t border-dashed border-black mt-1 pt-1">
                    <span>NET REFUND RECEIVED</span>
                    <span>Rs {{ number_format($purchaseReturn->received_amount, 2) }}</span>
                </div>
            @else
                <div class="flex justify-between text-xs font-bold border-t border-dashed border-black mt-1 pt-1">
                    <span>TOTAL REFUND RECEIVED</span>
                    <span>Rs {{ number_format($purchaseReturn->received_amount, 2) }}</span>
                </div>
            @endif
        </div>

        @if (!empty($purchaseReturn->notes))
            <div class="receipt-divider"></div>
            <div class="text-[10px]">
                <span class="font-bold">Note:</span> {{ $purchaseReturn->notes }}
            </div>
        @endif

        <div class="receipt-divider"></div>

        <!-- Verification: Barcode + QR -->
        <div class="flex items-center justify-between gap-2 my-2">
            <div class="text-left">
                <x-barcode-placeholder :value="$invoiceNumber" :width="140" :height="38" />
            </div>
            <div class="text-center flex-shrink-0">
                <x-qrcode-placeholder :value="$invoiceNumber" :size="55" />
                <p class="text-[7px] text-black/60 mt-0.5">Scan to verify</p>
            </div>
        </div>

        <div class="receipt-divider"></div>

        <!-- Footer -->
        <div class="text-center text-[10px] mt-2">
            <p>Total Units Returned: {{ $purchaseReturn->purchaseReturnItems->sum('qty') }}</p>
            <p class="mt-2 font-semibold">Stock Reversed — Purchase Return Processed</p>
            <p class="mt-1 text-[9px]">Printed: {{ now()->format('d-M-Y h:i A') }}</p>
        </div>

    </div>
</div>
