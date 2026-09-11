@php
    $shopName = config('app.shop_name', 'Tawakkal mart');
    $shopAddress = config('app.shop_address', 'Main Bazaar Road, Hyderabad');
    $shopPhone = config('app.shop_phone', '0300-1234567');

    // Gross = net total + discount (matches how the create page derives
    // Net Total from Subtotal - Discounts, just run in reverse here).
    $grossTotal = (float) $purchase->total_amount + (float) $purchase->discount_amount;
@endphp

<style>
    /* Scoped to the purchase thermal receipt */
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
        <p class="text-xs font-semibold uppercase">PURCHASE RECEIPT</p>
        <p class="text-sm font-bold uppercase tracking-wide">{{ $shopName }}</p>
        <p class="text-[10px]">{{ $shopAddress }}</p>
        <p class="text-[10px]">Tel: {{ $shopPhone }}</p>
    </div>

    <div class="receipt-divider"></div>

    <!-- Purchase Meta & Supplier Info -->
    <div class="flex justify-between"><span>Inv #</span><span class="font-bold">{{ $purchase->invoice_number }}</span></div>
    @if ($purchase->reference_number)
        <div class="flex justify-between"><span>Ref #</span><span class="font-mono">{{ $purchase->reference_number }}</span></div>
    @endif
    <div class="flex justify-between">
        <span>Date</span><span>{{ $purchase->date ? $purchase->date->format('d-M-Y h:i A') : now()->format('d-M-Y h:i A') }}</span>
    </div>
    <div class="flex justify-between"><span>Supplier</span><span class="font-bold">{{ $purchase->supplier->name ?? 'N/A' }}</span></div>
    @if ($purchase->supplier->phone_number ?? false)
        <div class="flex justify-between"><span>Phone</span><span>{{ $purchase->supplier->phone_number }}</span></div>
    @endif

    <div class="receipt-divider"></div>

    <!-- Purchase Items -->
    @foreach ($purchase->purchaseItems as $item)
        <div class="mb-1.5">
            <div class="flex justify-between font-semibold">
                <span>{{ $item->product->name ?? 'Deleted Product' }}</span>
            </div>
            <div class="flex justify-between text-[10px]">
                <span>
                    {{ rtrim(rtrim(number_format($item->qty, 2), '0'), '.') }}
                    @if ($item->bonus_qty > 0)
                        + {{ rtrim(rtrim(number_format($item->bonus_qty, 2), '0'), '.') }} free
                    @endif
                    x Rs {{ number_format($item->unit_cost, 2) }}
                    @if ($item->discount_amount > 0)
                        (-{{ number_format($item->discount_amount, 2) }})
                    @endif
                </span>
                {{-- subtotal_amount is the stored, validated line total from
                     purchase creation (qty x unit_cost - discount_amount) —
                     using it directly instead of recomputing keeps this
                     receipt guaranteed to match what was actually saved. --}}
                <span class="font-semibold">Rs {{ number_format($item->subtotal_amount, 2) }}</span>
            </div>
        </div>
    @endforeach

    <div class="receipt-divider"></div>

    <!-- Totals & Payments -->
    <div class="flex justify-between"><span>Gross Total</span><span>Rs {{ number_format($grossTotal, 2) }}</span></div>

    @if ($purchase->discount_amount > 0)
        <div class="flex justify-between"><span>Discount</span><span>-Rs {{ number_format($purchase->discount_amount, 2) }}</span></div>
    @endif

    <div class="flex justify-between text-sm font-bold border-t border-dashed border-black mt-1 pt-1">
        <span>Net Total</span><span>Rs {{ number_format($purchase->total_amount, 2) }}</span>
    </div>

    @if (!is_null($purchase->paid_amount))
        <div class="flex justify-between mt-1"><span>Paid</span><span>Rs {{ number_format($purchase->paid_amount, 2) }}</span></div>
        <div class="flex justify-between"><span>Balance Due</span><span>Rs {{ number_format($purchase->remaining_amount, 2) }}</span></div>
    @endif

    <div class="receipt-divider"></div>

    <!-- Verification: Barcode + QR -->
    <div class="flex items-center justify-between gap-2 my-2">
        <div class="text-left">
            <x-barcode-placeholder :value="$purchase->invoice_number" :width="140" :height="38" />
        </div>
        <div class="text-center flex-shrink-0">
            <x-qrcode-placeholder :value="$purchase->invoice_number" :size="55" />
            <p class="text-[7px] text-black/60 mt-0.5">Scan to verify</p>
        </div>
    </div>

    <div class="receipt-divider"></div>

    <!-- Footer Summary -->
    <div class="text-center text-[10px] mt-2">
        <p>
            Total Units: {{ $purchase->purchaseItems->sum('qty') }}
            @if ($purchase->total_bonus_qty > 0)
                (+{{ $purchase->total_bonus_qty }} Bonus)
            @endif
        </p>
        <p class="mt-2 font-semibold">Stock Inward Voucher</p>
        <p class="mt-1">{{ now()->format('d-M-Y h:i A') }}</p>
    </div>

</div>
