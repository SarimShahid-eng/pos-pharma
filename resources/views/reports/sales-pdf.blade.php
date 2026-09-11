@php
    $shopName = config('app.shop_name', 'Tawakkal Mart');
    $shopAddress = config('app.shop_address', 'Main Bazaar Road, Hyderabad');
    $shopPhone = config('app.shop_phone', '0300-1234567');

    $statementRef = 'SLS-' . strtoupper(substr(md5($fromDate . $toDate), 0, 8));
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Sales Report - {{ $fromDate }} to {{ $toDate }}</title>

    <style>
        @page { margin: 34px 34px 50px 34px; }
        * { box-sizing: border-box; }

        body {
            margin: 0; padding: 0;
            font-family: Helvetica, Arial, sans-serif;
            font-size: 9px; color: #20261d; line-height: 1.45; background: #ffffff;
        }

        table { border-collapse: collapse; width: 100%; table-layout: fixed; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
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
        .meta-period { color: #1e3a2b; font-family: 'Courier New', Courier, monospace; font-size: 9px; font-weight: bold; }
        .meta-sub { margin-top: 3px; color: #6b7264; font-family: 'Courier New', Courier, monospace; font-size: 8.5px; }

        .summary-ribbon { border-top: 0.75px solid #1e3a2b; border-bottom: 0.75px solid #1e3a2b; margin-bottom: 28px; }
        .summary-cell { width: 25%; padding: 12px 16px; border-right: 0.5px solid #d8d5c8; vertical-align: top; }
        .summary-cell.last { border-right: none; }
        .summary-cell.featured { border-bottom: 2.5px solid #1e3a2b; }
        .summary-label { color: #8a9086; font-size: 7px; font-weight: bold; letter-spacing: 0.9px; text-transform: uppercase; }
        .summary-value { margin-top: 8px; font-family: 'Courier New', Courier, monospace; font-size: 13px; font-weight: bold; white-space: nowrap; }
        .summary-status { margin-top: 3px; font-size: 7px; letter-spacing: 0.3px; }

        .section-title {
            color: #1e3a2b; font-size: 8.5px; font-weight: bold; letter-spacing: 1.4px;
            text-transform: uppercase; padding-bottom: 6px; border-bottom: 1.25px solid #1e3a2b;
            margin: 22px 0 10px 0;
        }

        .report-table thead th {
            padding: 8px 7px; border-bottom: 1.25px solid #1e3a2b; color: #6b7264;
            font-size: 7px; font-weight: bold; letter-spacing: 0.7px; text-transform: uppercase; text-align: left;
        }
        .report-table tbody td {
            padding: 6px 7px; border-bottom: 0.5px solid #e5e2d6; font-size: 8px; vertical-align: middle;
        }
        .report-table tbody tr { page-break-inside: avoid; }
        .report-table tfoot td {
            padding: 9px 7px; border-top: 1.75px double #1e3a2b; font-size: 8.5px; font-weight: bold;
        }
        .amount { font-family: 'Courier New', Courier, monospace; font-weight: bold; white-space: nowrap; }
        .empty-note { color: #b0b5a8; font-style: italic; font-size: 8.5px; padding: 10px 0; }

        .footer {
            position: fixed; bottom: -36px; left: 0; right: 0; width: 100%;
            padding-top: 9px; border-top: 0.5px solid #d8d5c8; color: #b0b5a8; font-size: 7px;
        }
        .footer strong { color: #8a9086; }
        .footer-note { margin-top: 4px; color: #d3d0c3; font-size: 6.5px; }
        .page-number:after { content: counter(page) " / " counter(pages); }
    </style>
</head>

<body>

    <div class="masthead-rule-heavy"></div>
    <div class="masthead-rule-light"></div>

    <table cellpadding="0" cellspacing="0" style="table-layout: auto;">
        <tr>
            <td width="58%" style="vertical-align: top;">
                <div class="brand-name">{{ $shopName }}</div>
                <div class="brand-tagline">General Store &amp; Wholesale Supply</div>
                <div class="brand-details">{{ $shopAddress }}<br>{{ $shopPhone }}</div>
            </td>
            <td width="42%" class="text-right" style="vertical-align: top;">
                <div class="doc-label">Financial Report</div>
                <div class="doc-title">Sales Report</div>
                <div class="doc-ref">{{ $statementRef }}</div>
            </td>
        </tr>
    </table>

    <table class="meta-block" cellpadding="0" cellspacing="0" style="table-layout: auto;">
        <tr>
            <td width="50%" style="vertical-align: top;">
                <div class="meta-label">Report Period</div>
                <div class="meta-period" style="margin-top: 5px;">
                    {{ \Carbon\Carbon::parse($fromDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($toDate)->format('d M Y') }}
                </div>
            </td>
            <td width="50%" class="text-right" style="vertical-align: top;">
                <div class="meta-label">Generated</div>
                <div class="meta-sub" style="margin-top: 5px;">{{ now()->format('d M Y, h:i A') }}</div>
            </td>
        </tr>
    </table>

    <table class="summary-ribbon" cellpadding="0" cellspacing="0">
        <tr>
            <td class="summary-cell">
                <div class="summary-label">Gross Sales</div>
                <div class="summary-value forest">Rs {{ number_format($totalSalesGross, 2) }}</div>
                <div class="summary-status muted">{{ $sales->count() }} invoice(s)</div>
            </td>
            <td class="summary-cell">
                <div class="summary-label">Sale Returns</div>
                <div class="summary-value tag-red">Rs {{ number_format($totalSaleReturnsValue, 2) }}</div>
                <div class="summary-status muted">{{ $saleReturns->count() }} return(s)</div>
            </td>
            <td class="summary-cell">
                <div class="summary-label">Refunded</div>
                <div class="summary-value tag-red">Rs {{ number_format($totalRefunded, 2) }}</div>
                <div class="summary-status muted">Cash/credit given back</div>
            </td>
            <td class="summary-cell last featured">
                <div class="summary-label">Net Sales</div>
                <div class="summary-value forest">Rs {{ number_format($netSales, 2) }}</div>
                <div class="summary-status forest">Net total minus refunds</div>
            </td>
        </tr>
    </table>

    <div class="section-title">Sales ({{ $sales->count() }})</div>

    <table class="report-table" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th width="16%">Invoice #</th>
                <th width="22%">Date</th>
                <th width="14%">Payment</th>
                <th width="16%" class="text-center">Gross</th>
                <th width="16%" class="text-center">Discount</th>
                <th width="16%" class="text-center">Net Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($sales as $sale)
                <tr>
                    <td class="font-mono forest">{{ $sale->invoice_number }}</td>
                    <td class="font-mono muted">{{ \Carbon\Carbon::parse($sale->date)->format('d/m/Y h:i A') }}</td>
                    <td style="text-transform: uppercase;">{{ $sale->payment_method }}</td>
                    <td class="text-center amount">Rs {{ number_format($sale->total_amount, 2) }}</td>
                    <td class="text-center amount tag-red">
                        {{ $sale->discount_amount > 0 ? '-Rs ' . number_format($sale->discount_amount, 2) : '-' }}
                    </td>
                    <td class="text-center amount">Rs {{ number_format($sale->net_amount, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="empty-note">No sales recorded in this period.</td></tr>
            @endforelse
        </tbody>
        @if ($sales->isNotEmpty())
            <tfoot>
                <tr>
                    <td colspan="3">Totals</td>
                    <td class="text-center amount">Rs {{ number_format($totalSalesGross, 2) }}</td>
                    <td class="text-center amount tag-red">{{ $totalSalesDiscount > 0 ? '-Rs ' . number_format($totalSalesDiscount, 2) : '-' }}</td>
                    <td class="text-center amount forest">Rs {{ number_format($totalSalesNet, 2) }}</td>
                </tr>
            </tfoot>
        @endif
    </table>

    <div class="section-title">Sale Returns ({{ $saleReturns->count() }})</div>

    <table class="report-table" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th width="16%">Return #</th>
                <th width="20%">Orig. Invoice</th>
                <th width="24%">Date</th>
                <th width="20%" class="text-right">Return Value</th>
                <th width="20%" class="text-right">Refunded</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($saleReturns as $return)
                <tr>
                    <td class="font-mono tag-red">#{{ str_pad($return->id, 6, '0', STR_PAD_LEFT) }}</td>
                    <td class="font-mono">{{ $return->sale->invoice_number ?? '—' }}</td>
                    <td class="font-mono muted">{{ \Carbon\Carbon::parse($return->date)->format('d/m/Y h:i A') }}</td>
                    <td class="text-center amount">Rs {{ number_format($return->total_amount, 2) }}</td>
                    <td class="text-center amount tag-red">Rs {{ number_format($return->refunded_amount, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="empty-note">No sale returns recorded in this period.</td></tr>
            @endforelse
        </tbody>
        @if ($saleReturns->isNotEmpty())
            <tfoot>
                <tr>
                    <td colspan="3">Totals</td>
                    <td class="text-center amount">Rs {{ number_format($totalSaleReturnsValue, 2) }}</td>
                    <td class="text-center amount tag-red">Rs {{ number_format($totalRefunded, 2) }}</td>
                </tr>
            </tfoot>
        @endif
    </table>

    <div class="footer">
        <table cellpadding="0" cellspacing="0" style="table-layout: auto;">
            <tr>
                <td width="70%" style="vertical-align: top;">{{ $shopName }}</td>
                <td width="30%" class="text-center" style="vertical-align: top;">Page <span class="page-number"></span></td>
            </tr>
            <tr>
                <td colspan="2" class="footer-note">This report is computer-generated and does not require a signature.</td>
            </tr>
        </table>
    </div>

</body>
</html>