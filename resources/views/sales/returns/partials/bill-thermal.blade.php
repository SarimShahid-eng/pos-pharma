@php
    $shopName = config('app.shop_name', 'Tawakkal Mart');
    $shopAddress = config('app.shop_address', 'Main Bazaar Road, Hyderabad');
    $shopPhone = config('app.shop_phone', '0300-1234567');

    $originalSale = $saleReturn->sale;
    $invoiceNumber = $originalSale->invoice_number ?? 'N/A';

    $returnDate = $saleReturn->formatted_date
        ?  $saleReturn->formatted_date
        : now()->format('d-M-Y h:i A');
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
            <p class="text-xs font-bold uppercase tracking-wider mt-1 border-y border-black py-0.5">*** RETURN RECEIPT
                ***</p>
        </div>

        <div class="receipt-divider"></div>

        <!-- Meta Data -->
        <div class="flex justify-between"><span>Orig. Invoice #</span><span class="font-bold">{{ $invoiceNumber }}</span>
        </div>
        <div class="flex justify-between"><span>Return Date</span><span>{{ $returnDate }}</span></div>

        @if (!empty($originalSale->customer->name))
            <div class="flex justify-between"><span>Customer</span><span>{{ $originalSale->customer->name }}</span>
            </div>
        @endif

        <div class="receipt-divider"></div>

        <!-- Returned Items Breakdown -->
        <div class="flex justify-between text-[10px] font-bold border-b border-black pb-1 mb-1">
            <span>ITEM / QTY BREAKDOWN</span>
            <span>REFUND AMOUNT</span>
        </div>

        @foreach ($saleReturn->saleReturnItems as $item)
            @php
                $baseRate = (float) ($item->rate ?? 0);
                $flatDiscount = (float) ($item->discount ?? 0);
                $originalQty = (float) ($item->saleItem->qty ?? 0);
                $returnedQty = (float) $item->qty;
                $remainingQty = $originalQty > 0 ? max(0, $originalQty - $returnedQty) : null;

                // Derive Unit Effective Rate
                if (isset($item->after_discount_price) && $item->after_discount_price > 0) {
                    $effectiveRate = (float) $item->after_discount_price;
                } elseif ($originalQty > 0) {
                    $effectiveRate = max(0, $originalQty * $baseRate - $flatDiscount) / $originalQty;
                } else {
                    $effectiveRate = max(0, $baseRate - $flatDiscount);
                }

                // Line Refund Amount
                $lineRefundAmount = $item->amount ?? round($returnedQty * $effectiveRate, 2);

                // Per-unit proportional discount for display
                $unitDiscount = $originalQty > 0 ? $flatDiscount / $originalQty : $flatDiscount;
                $totalItemDiscount = $returnedQty * $unitDiscount;
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
                            <span>• Orig. Sold Qty:</span>
                            <span>{{ rtrim(rtrim(number_format($originalQty, 2), '0'), '.') }}</span>
                        </div>
                    @endif

                    <div class="flex justify-between font-semibold">
                        <span>• Returned Qty:</span>
                        <span>-{{ rtrim(rtrim(number_format($returnedQty, 2), '0'), '.') }}</span>
                    </div>

                    @if (!is_null($remainingQty))
                        <div class="flex justify-between text-[9px] text-black/70">
                            <span>• Kept/Remaining:</span>
                            <span>{{ rtrim(rtrim(number_format($remainingQty, 2), '0'), '.') }}</span>
                        </div>
                    @endif
                </div>

                <!-- Rate & Discount Details -->
                @if ($totalItemDiscount > 0)
                    <div class="flex justify-between text-[10px] text-black/70 pl-1">
                        <span>Orig. Rate ({{ rtrim(rtrim(number_format($returnedQty, 2), '0'), '.') }} x Rs
                            {{ number_format($baseRate, 2) }})</span>
                        <span>Rs {{ number_format($returnedQty * $baseRate, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-[10px] text-black/70 pl-1">
                        <span>Less Disc (-Rs {{ number_format($unitDiscount, 2) }}/unit)</span>
                        <span>-Rs {{ number_format($totalItemDiscount, 2) }}</span>
                    </div>
                @endif

                <!-- Final Line Refund Total -->
                <div class="flex justify-between text-[10px] font-bold border-t border-dotted border-black/30 pt-0.5">
                    <span>Net Price ({{ rtrim(rtrim(number_format($returnedQty, 2), '0'), '.') }} x Rs
                        {{ number_format($effectiveRate, 2) }})</span>
                    <span>Rs {{ number_format($lineRefundAmount, 2) }}</span>
                </div>
            </div>
        @endforeach

        <!-- Original Invoice Financial Reference -->
        @if ($originalSale)
            <div class="receipt-divider"></div>
            <div class="text-[9px] text-black/80 space-y-0.5 bg-black/5 p-1 rounded">
                <div class="font-bold text-[10px] uppercase border-b border-black/20 pb-0.5 mb-0.5">Original Sale
                    Reference</div>
                <div class="flex justify-between">
                    <span>Orig. Gross Total:</span>
                    <span>Rs {{ number_format($originalSale->total_amount ?? 0, 2) }}</span>
                </div>
                @if (($originalSale->discount_amount ?? 0) > 0)
                    <div class="flex justify-between">
                        <span>Orig. Total Discount:</span>
                        <span>-Rs {{ number_format($originalSale->discount_amount, 2) }}</span>
                    </div>
                @endif
                <div class="flex justify-between font-bold border-t border-dotted border-black/20 pt-0.5">
                    <span>Orig. Net Paid:</span>
                    <span>Rs
                        {{ number_format($originalSale->net_amount ?? ($originalSale->total_amount ?? 0) - ($originalSale->discount_amount ?? 0), 2) }}</span>
                </div>
            </div>
        @endif

        <div class="receipt-divider"></div>

        <!-- Return Financial Summary -->
        <div class="space-y-0.5">
            @if ($saleReturn->total_amount > $saleReturn->refunded_amount)
                <div class="flex justify-between text-[10px]">
                    <span>Returned Value (After Disc)</span>
                    <span>Rs {{ number_format($saleReturn->total_amount, 2) }}</span>
                </div>

                <div class="flex justify-between text-[10px] text-black">
                    <span>Less: Restocking / Deductions</span>
                    <span>-Rs {{ number_format($saleReturn->total_amount - $saleReturn->refunded_amount, 2) }}</span>
                </div>

                <div class="flex justify-between text-xs font-bold border-t border-dashed border-black mt-1 pt-1">
                    <span>NET REFUND PAID</span>
                    <span>Rs {{ number_format($saleReturn->refunded_amount, 2) }}</span>
                </div>
            @else
                <div class="flex justify-between text-xs font-bold border-t border-dashed border-black mt-1 pt-1">
                    <span>TOTAL REFUND PAID</span>
                    <span>Rs {{ number_format($saleReturn->refunded_amount, 2) }}</span>
                </div>
            @endif
        </div>

        @if (!empty($saleReturn->notes))
            <div class="receipt-divider"></div>
            <div class="text-[10px]">
                <span class="font-bold">Note:</span> {{ $saleReturn->notes }}
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
            <p>Total Units Returned: {{ $saleReturn->saleReturnItems->sum('qty') }}</p>
            <p class="mt-2 font-semibold">Return Processed Successfully</p>
            <p class="mt-1 text-[9px]">Printed: {{ now()->format('d-M-Y h:i A') }}</p>
        </div>

    </div>
</div>
