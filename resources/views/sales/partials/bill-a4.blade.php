@php
    // Swap these for your real shop details, or pull from a settings table / config.
    $shopName = config('app.shop_name', 'Tawakkal mart');
    $shopAddress = config('app.shop_address', 'Main Bazaar Road, Hyderabad');
    $shopPhone = config('app.shop_phone', '0300-1234567');
@endphp

<style>
    /* Scoped to the thermal receipt only */
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

<div class="receipt">

    <!-- Shop Header -->
    <div class="text-center">
        <p class="text-sm font-bold uppercase tracking-wide">{{ $shopName }}</p>
        <p class="text-[10px]">{{ $shopAddress }}</p>
        <p class="text-[10px]">Tel: {{ $shopPhone }}</p>
    </div>

    <div class="receipt-divider"></div>

    <!-- Invoice Meta -->
    <div class="flex justify-between"><span>Invoice #</span><span class="font-bold">{{ $sale->invoice_number }}</span>
    </div>
    <div class="flex justify-between"><span>Date</span><span>{{ $sale->formatted_date }}</span></div>
    <div class="flex justify-between"><span>Payment</span><span class="uppercase">{{ $sale->payment_method }}</span>
    </div>

    <div class="receipt-divider"></div>

    <!-- Items -->
    @foreach ($sale->saleItems as $item)
        <div class="mb-1.5">
            <div class="flex justify-between font-semibold">
                <span>{{ $item->product->name ?? 'Deleted Product' }}</span>
            </div>
            <div class="flex justify-between text-[10px]">
                <span>
                    {{ rtrim(rtrim(number_format($item->qty, 2), '0'), '.') }}
                    x Rs {{ number_format($item->rate, 2) }}
                    @if ($item->discount > 0)
                        (-{{ number_format($item->discount, 2) }})
                    @endif
                </span>
                <span class="font-semibold">Rs {{ number_format($item->amount, 2) }}</span>
            </div>
        </div>
    @endforeach

    <div class="receipt-divider"></div>

    <!-- Totals -->
    <div class="flex justify-between"><span>Gross Total</span><span>Rs
            {{ number_format($sale->total_amount, 2) }}</span></div>
    @if ($sale->discount_amount > 0)
        <div class="flex justify-between"><span>Discount</span><span>-Rs
                {{ number_format($sale->discount_amount, 2) }}</span></div>
    @endif
    <div class="flex justify-between text-sm font-bold border-t border-dashed border-black mt-1 pt-1">
        <span>Net Total</span><span>Rs {{ number_format($sale->net_amount, 2) }}</span>
    </div>

    @if (!is_null($sale->received_amount) && $sale->received_amount > 0)
        <div class="flex justify-between mt-1"><span>Received</span><span>Rs
                {{ number_format($sale->received_amount, 2) }}</span></div>
        {{-- <div class="flex justify-between"><span>Change</span><span>Rs
                {{ number_format($sale->change_given, 2) }}</span></div> --}}
    @endif

    <div class="receipt-divider"></div>

    <!-- Verification: Barcode + QR (temporary placeholders) -->
    <div class="flex items-center justify-between gap-2 my-2">
        <div class="text-left">
            <x-barcode-placeholder :value="$sale->invoice_number" :width="140" :height="38" />
        </div>
        <div class="text-center flex-shrink-0">
            <x-qrcode-placeholder :value="$sale->invoice_number" :size="55" />
            <p class="text-[7px] text-black/60 mt-0.5">Scan to verify</p>
        </div>
    </div>

    <div class="receipt-divider"></div>

    <!-- Footer -->
    <div class="text-center text-[10px] mt-2">
        <p>Total Items: {{ $sale->saleItems->sum('qty') }}</p>
        <p class="mt-2 font-semibold">Thank you for shopping with us!</p>
        <p>Goods once sold are not returnable without receipt.</p>
        <p class="mt-2">{{ now()->format('d-M-Y h:i A') }}</p>
    </div>

</div>
