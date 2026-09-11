@php
    $shopName = config('app.shop_name', 'Tawakkal Mart');
    $shopAddress = config('app.shop_address', 'Main Bazaar Road, Hyderabad');
    $shopPhone = config('app.shop_phone', '0300-1234567');

    $supplierName = $supplier->name ?? 'N/A';
    $supplierPhone = $supplier->phone_number ?? 'N/A';

    $openingLabel = $openingBalance >= 0 ? 'Cr' : 'Dr';
    $closingLabel = $closingBalance >= 0 ? 'Cr' : 'Dr';
    $closingColor = $closingBalance >= 0 ? '#b23a2e' : '#1e3a2b';
    $openingColor = $openingBalance >= 0 ? '#b23a2e' : '#1e3a2b';

    $statementRef = 'INV-' . strtoupper(substr(md5($supplier->id . $fromDate . $toDate), 0, 8));
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Supplier Invoice - {{ $supplierName }}</title>

    <style>
        @page { margin: 34px 34px 50px 34px; }
        * { box-sizing: border-box; }

        body {
            margin: 0;
            padding: 0;
            font-family: Helvetica, Arial, sans-serif;
            font-size: 9px;
            color: #20261d;
            line-height: 1.45;
            background: #ffffff;
        }

        table { border-collapse: collapse; width: 100%; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-mono { font-family: 'Courier New', Courier, monospace; }
        .forest { color: #1e3a2b; }
        .tag-red { color: #b23a2e; }
        .muted { color: #8a9086; }

        .masthead-rule-heavy { border-top: 2px solid #1e3a2b; font-size: 0; line-height: 0; }
        .masthead-rule-light { border-top: 0.75px solid #1e3a2b; font-size: 0; line-height: 0; margin-bottom: 22px; }

        .brand-name { font-family: Georgia, 'Times New Roman', serif; font-size: 21px; font-weight: bold; color: #1e3a2b; letter-spacing: -0.3px; }
        .brand-tagline { margin-top: 2px; color: #8a9086; font-size: 7.5px; letter-spacing: 1px; text-transform: uppercase; }
        .brand-details { margin-top: 10px; color: #6b7264; font-size: 8px; line-height: 1.6; }

        .doc-label { color: #8a9086; font-size: 7.5px; font-weight: bold; letter-spacing: 1.6px; text-transform: uppercase; }
        .doc-title { margin-top: 3px; color: #1e3a2b; font-family: Georgia, 'Times New Roman', serif; font-size: 16px; font-weight: bold; }
        .doc-ref { margin-top: 9px; color: #6b7264; font-family: 'Courier New', Courier, monospace; font-size: 8px; }

        .meta-block { border-top: 0.75px solid #d8d5c8; border-bottom: 0.75px solid #d8d5c8; padding: 13px 0; margin: 22px 0 26px 0; }
        .meta-label { color: #8a9086; font-size: 7px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; }
        .meta-value { margin-top: 4px; color: #1e3a2b; font-size: 12.5px; font-weight: bold; }
        .meta-sub { margin-top: 3px; color: #6b7264; font-family: 'Courier New', Courier, monospace; font-size: 8.5px; }
        .meta-period { color: #1e3a2b; font-family: 'Courier New', Courier, monospace; font-size: 9px; font-weight: bold; }

        .summary-ribbon { border-top: 0.75px solid #1e3a2b; border-bottom: 0.75px solid #1e3a2b; margin-bottom: 28px; }
        .summary-cell { width: 25%; padding: 12px 16px; border-right: 0.5px solid #d8d5c8; vertical-align: top; }
        .summary-cell.last { border-right: none; }
        .summary-cell.featured { border-bottom: 2.5px solid {{ $closingColor }}; }
        .summary-label { color: #8a9086; font-size: 7px; font-weight: bold; letter-spacing: 0.9px; text-transform: uppercase; }
        .summary-value { margin-top: 8px; font-family: 'Courier New', Courier, monospace; font-size: 13px; font-weight: bold; white-space: nowrap; }
        .summary-status { margin-top: 3px; font-size: 7px; letter-spacing: 0.3px; }

        .section-title {
            color: #1e3a2b; font-size: 8.5px; font-weight: bold; letter-spacing: 1.4px;
            text-transform: uppercase; padding-bottom: 6px; border-bottom: 1.25px solid #1e3a2b;
            margin: 22px 0 12px 0;
        }

        /* Per-invoice / per-return block */
        .doc-block { margin-bottom: 16px; page-break-inside: avoid; }
        .doc-block-head {
            border-bottom: 0.75px solid #1e3a2b;
            padding-bottom: 5px;
            margin-bottom: 6px;
        }
        .doc-block-ref { font-family: 'Courier New', Courier, monospace; font-size: 9.5px; font-weight: bold; color: #1e3a2b; }
        .doc-block-date { color: #8a9086; font-size: 7.5px; margin-top: 1px; }
        .doc-block-total { font-family: 'Courier New', Courier, monospace; font-size: 10px; font-weight: bold; }

        .item-table thead th {
            padding: 5px 6px; border-bottom: 0.75px solid #d8d5c8; color: #8a9086;
            font-size: 6.5px; font-weight: bold; letter-spacing: 0.6px; text-transform: uppercase; text-align: left;
        }
        .item-table tbody td {
            padding: 5px 6px; border-bottom: 0.5px solid #f0eee5; font-size: 8px;
        }
        .item-table tfoot td {
            padding: 6px; border-top: 0.75px solid #1e3a2b; font-size: 8px; font-weight: bold;
        }

        .amount { font-family: 'Courier New', Courier, monospace; font-weight: bold; white-space: nowrap; }

        .due-block { margin-top: 26px; padding-top: 14px; border-top: 0.75px solid #d8d5c8; }
        .due-message { color: #6b7264; font-size: 9px; }
        .due-amount-label { color: #8a9086; font-size: 7.5px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; }
        .due-amount { color: {{ $closingColor }}; font-family: 'Courier New', Courier, monospace; font-size: 20px; font-weight: bold; letter-spacing: -0.3px; }

        .footer {
            position: fixed; bottom: -36px; left: 0; right: 0; width: 100%;
            padding-top: 9px; border-top: 0.5px solid #d8d5c8; color: #b0b5a8; font-size: 7px;
        }
        .footer strong { color: #8a9086; }
        .legend-cr { color: #b23a2e; font-weight: bold; }
        .legend-dr { color: #1e3a2b; font-weight: bold; }
        .footer-note { margin-top: 4px; color: #d3d0c3; font-size: 6.5px; }
        .page-number:after { content: counter(page) " / " counter(pages); }

        .empty-note { color: #b0b5a8; font-style: italic; font-size: 8.5px; padding: 10px 0; }
    </style>
</head>

<body>

    <div class="masthead-rule-heavy"></div>
    <div class="masthead-rule-light"></div>

    <table cellpadding="0" cellspacing="0">
        <tr>
            <td width="58%" style="vertical-align: top;">
                <div class="brand-name">{{ $shopName }}</div>
                <div class="brand-tagline">General Store &amp; Wholesale Supply</div>
                <div class="brand-details">{{ $shopAddress }}<br>{{ $shopPhone }}</div>
            </td>
            <td width="42%" class="text-right" style="vertical-align: top;">
                <div class="doc-label">Financial Statement</div>
                <div class="doc-title">Supplier Invoice</div>
                <div class="doc-ref">{{ $statementRef }}</div>
            </td>
        </tr>
    </table>

    <table class="meta-block" cellpadding="0" cellspacing="0">
        <tr>
            <td width="45%" style="vertical-align: top;">
                <div class="meta-label">Statement For</div>
                <div class="meta-value">{{ $supplierName }}</div>
                <div class="meta-sub">{{ $supplierPhone }}</div>
            </td>
            <td width="30%" style="vertical-align: top;">
                <div class="meta-label">Statement Period</div>
                <div class="meta-period" style="margin-top: 5px;">
                    {{ \Carbon\Carbon::parse($fromDate)->format('d M Y') }} &ndash; {{ \Carbon\Carbon::parse($toDate)->format('d M Y') }}
                </div>
            </td>
            <td width="25%" class="text-right" style="vertical-align: top;">
                <div class="meta-label">Generated</div>
                <div class="meta-sub" style="margin-top: 5px;">{{ now()->format('d M Y, h:i A') }}</div>
            </td>
        </tr>
    </table>

    <table class="summary-ribbon" cellpadding="0" cellspacing="0">
        <tr>
            <td class="summary-cell">
                <div class="summary-label">Opening Balance</div>
                <div class="summary-value" style="color: {{ $openingColor }};">
                    Rs {{ number_format(abs($openingBalance), 2) }} <span style="font-size: 8px;">{{ $openingLabel }}</span>
                </div>
                <div class="summary-status muted">As of {{ \Carbon\Carbon::parse($fromDate)->format('d M Y') }}</div>
            </td>
            <td class="summary-cell">
                <div class="summary-label">Total Payments</div>
                <div class="summary-value forest">Rs {{ number_format($totalDebit, 2) }}</div>
                <div class="summary-status muted">Received by supplier</div>
            </td>
            <td class="summary-cell">
                <div class="summary-label">Total Purchases</div>
                <div class="summary-value tag-red">Rs {{ number_format($totalCredit, 2) }}</div>
                <div class="summary-status muted">Invoiced this period</div>
            </td>
            <td class="summary-cell last featured">
                <div class="summary-label">Closing Balance</div>
                <div class="summary-value" style="color: {{ $closingColor }};">
                    Rs {{ number_format(abs($closingBalance), 2) }} <span style="font-size: 8px;">{{ $closingLabel }}</span>
                </div>
                <div class="summary-status" style="color: {{ $closingColor }};">
                    {{ $closingBalance >= 0 ? 'Amount Payable' : 'Advance Balance' }}
                </div>
            </td>
        </tr>
    </table>

    {{-- ================= PURCHASES ================= --}}
    <div class="section-title">Purchases ({{ $purchases->count() }})</div>

    @forelse ($purchases as $purchase)
        @php $gross = (float) $purchase->total_amount + (float) $purchase->discount_amount; @endphp
        <div class="doc-block">
            <table class="doc-block-head" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="70%">
                        <div class="doc-block-ref">
                            {{ $purchase->invoice_number }}
                            @if ($purchase->reference_number)
                                <span class="muted" style="font-weight: normal;">&middot; Ref: {{ $purchase->reference_number }}</span>
                            @endif
                        </div>
                        <div class="doc-block-date">{{ \Carbon\Carbon::parse($purchase->date)->format('d M Y') }}</div>
                    </td>
                    <td width="30%" class="text-right">
                        <div class="doc-block-total forest">Rs {{ number_format($purchase->total_amount, 2) }}</div>
                    </td>
                </tr>
            </table>

            <table class="item-table" cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th width="40%">Product</th>
                        <th width="12%" class="text-center">Qty</th>
                        <th width="18%" class="text-right">Unit Cost</th>
                        <th width="15%" class="text-right">Discount</th>
                        <th width="15%" class="text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($purchase->purchaseItems as $item)
                        <tr>
                            <td>{{ $item->product->name ?? 'Deleted Product' }}</td>
                            <td class="text-center">{{ rtrim(rtrim(number_format($item->qty, 2), '0'), '.') }}</td>
                            <td class="text-right amount">Rs {{ number_format($item->unit_cost, 2) }}</td>
                            <td class="text-right amount">{{ $item->discount_amount > 0 ? '-Rs ' . number_format($item->discount_amount, 2) : '&ndash;' }}</td>
                            <td class="text-right amount forest">Rs {{ number_format($item->subtotal_amount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-right muted" style="font-weight: normal;">Gross: Rs {{ number_format($gross, 2) }}</td>
                        <td class="text-right tag-red">{{ $purchase->discount_amount > 0 ? '-Rs ' . number_format($purchase->discount_amount, 2) : '&ndash;' }}</td>
                        <td class="text-right forest">Rs {{ number_format($purchase->total_amount, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @empty
        <div class="empty-note">No purchases recorded in this period.</div>
    @endforelse

    {{-- ================= PURCHASE RETURNS ================= --}}
    <div class="section-title">Purchase Returns ({{ $purchaseReturns->count() }})</div>

    @forelse ($purchaseReturns as $return)
        <div class="doc-block">
            <table class="doc-block-head" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="70%">
                        <div class="doc-block-ref tag-red">
                            Return #{{ str_pad($return->id, 6, '0', STR_PAD_LEFT) }}
                            <span class="muted" style="font-weight: normal;">&middot; Orig. Invoice: {{ $return->purchase->invoice_number ?? '—' }}</span>
                        </div>
                        <div class="doc-block-date">{{ \Carbon\Carbon::parse($return->date)->format('d M Y') }}</div>
                    </td>
                    <td width="30%" class="text-right">
                        <div class="doc-block-total tag-red">Rs {{ number_format($return->received_amount, 2) }}</div>
                    </td>
                </tr>
            </table>

            <table class="item-table" cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th width="46%">Product</th>
                        <th width="18%" class="text-center">Qty Returned</th>
                        <th width="18%" class="text-right">Unit Cost</th>
                        <th width="18%" class="text-right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($return->purchaseReturnItems as $item)
                        <tr>
                            <td>{{ $item->product->name ?? 'Deleted Product' }}</td>
                            <td class="text-center">{{ rtrim(rtrim(number_format($item->qty, 2), '0'), '.') }}</td>
                            <td class="text-right amount">Rs {{ number_format($item->unit_cost, 2) }}</td>
                            <td class="text-right amount tag-red">Rs {{ number_format($item->amount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" class="text-right muted" style="font-weight: normal;">
                            @if ($return->total_amount > $return->received_amount)
                                Retained as Credit: -Rs {{ number_format($return->total_amount - $return->received_amount, 2) }}
                            @endif
                        </td>
                        <td class="text-right muted" style="font-weight: normal;">Return Value:</td>
                        <td class="text-right tag-red">Rs {{ number_format($return->received_amount, 2) }}</td>
                    </tr>
                </tfoot>
            </table>

            @if ($return->notes)
                <div style="margin-top: 4px; font-size: 7.5px; color: #6b7264;">
                    <strong style="color: #334155;">Note:</strong> {{ $return->notes }}
                </div>
            @endif
        </div>
    @empty
        <div class="empty-note">No returns recorded in this period.</div>
    @endforelse

    {{-- ================= AMOUNT DUE ================= --}}
    <table class="due-block" cellpadding="0" cellspacing="0">
        <tr>
            <td width="65%" style="vertical-align: bottom;">
                <div class="due-message">
                    @if ($closingBalance > 0)
                        This statement reflects an outstanding amount payable to the supplier.
                    @elseif ($closingBalance < 0)
                        This account carries an advance / credit balance in our favor.
                    @else
                        This account is fully settled as of the statement date.
                    @endif
                </div>
            </td>
            <td width="35%" class="text-right" style="vertical-align: bottom;">
                <div class="due-amount-label">Closing Balance</div>
                <div class="due-amount">Rs {{ number_format(abs($closingBalance), 2) }} {{ $closingLabel }}</div>
            </td>
        </tr>
    </table>

    <div class="footer">
        <table cellpadding="0" cellspacing="0">
            <tr>
                <td width="55%" style="vertical-align: top;">
                    <strong>Legend:</strong>
                    <span class="legend-cr">Cr</span> Amount payable to supplier
                    &nbsp;&nbsp;&middot;&nbsp;&nbsp;
                    <span class="legend-dr">Dr</span> Advance / credit balance
                </td>
                <td width="25%" class="text-right" style="vertical-align: top;">{{ $shopName }}</td>
                <td width="20%" class="text-right" style="vertical-align: top;">Page <span class="page-number"></span></td>
            </tr>
            <tr>
                <td colspan="3" class="footer-note">This statement is computer-generated and does not require a signature.</td>
            </tr>
        </table>
    </div>

</body>
</html>
