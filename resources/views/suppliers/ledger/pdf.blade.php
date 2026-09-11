@php
    $shopName = config('app.shop_name', 'Tawakkal Mart');
    $shopAddress = config('app.shop_address', 'Main Bazaar Road, Hyderabad');
    $shopPhone = config('app.shop_phone', '0300-1234567');

    $supplierName = $supplier->name ?? 'N/A';
    $supplierPhone = $supplier->phone_number ?? 'N/A';

    $openingLabel = $openingBalance >= 0 ? 'Cr' : 'Dr';
    $closingLabel = $closingBalance >= 0 ? 'Cr' : 'Dr';

    // One accent color, used with intent: forest for text/rules/favorable
    // amounts, tag-red ONLY where a balance is payable (Cr). No wheat, no
    // fills, no card backgrounds — the document is carried by typography
    // and hairline rules, not color blocks.
    $closingColor = $closingBalance >= 0 ? '#b23a2e' : '#1e3a2b';
    $openingColor = $openingBalance >= 0 ? '#b23a2e' : '#1e3a2b';

    $statementRef = 'STMT-' . strtoupper(substr(md5($supplier->id . $fromDate . $toDate), 0, 8));
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Supplier Statement - {{ $supplierName }}</title>

    <style>
        @page {
            margin: 34px 34px 50px 34px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: Helvetica, Arial, sans-serif;
            font-size: 9px;
            color: #20261d;
            line-height: 1.45;
            background: #ffffff;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .font-mono {
            font-family: 'Courier New', Courier, monospace;
        }

        .forest {
            color: #1e3a2b;
        }

        .tag-red {
            color: #b23a2e;
        }

        .muted {
            color: #8a9086;
        }

        /* ---------------------------------------------------------
           MASTHEAD — double hairline rule, not a solid color bar
        --------------------------------------------------------- */

        .masthead-rule-heavy {
            border-top: 2px solid #1e3a2b;
            font-size: 0;
            line-height: 0;
        }

        .masthead-rule-light {
            border-top: 0.75px solid #1e3a2b;
            font-size: 0;
            line-height: 0;
            margin-bottom: 22px;
        }

        /* ---------------------------------------------------------
           HEADER
        --------------------------------------------------------- */

        .brand-name {
            font-family: Georgia, 'Times New Roman', serif;
            font-size: 21px;
            font-weight: bold;
            color: #1e3a2b;
            letter-spacing: -0.3px;
        }

        .brand-tagline {
            margin-top: 2px;
            color: #8a9086;
            font-size: 7.5px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .brand-details {
            margin-top: 10px;
            color: #6b7264;
            font-size: 8px;
            line-height: 1.6;
        }

        .doc-label {
            color: #8a9086;
            font-size: 7.5px;
            font-weight: bold;
            letter-spacing: 1.6px;
            text-transform: uppercase;
        }

        .doc-title {
            margin-top: 3px;
            color: #1e3a2b;
            font-family: Georgia, 'Times New Roman', serif;
            font-size: 16px;
            font-weight: bold;
        }

        .doc-ref {
            margin-top: 9px;
            color: #6b7264;
            font-family: 'Courier New', Courier, monospace;
            font-size: 8px;
        }

        /* ---------------------------------------------------------
           STATEMENT META — plain text block, hairline rules top/bottom,
           no fill, no box
        --------------------------------------------------------- */

        .meta-block {
            border-top: 0.75px solid #d8d5c8;
            border-bottom: 0.75px solid #d8d5c8;
            padding: 13px 0;
            margin: 22px 0 26px 0;
        }

        .meta-label {
            color: #8a9086;
            font-size: 7px;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .meta-value {
            margin-top: 4px;
            color: #1e3a2b;
            font-size: 12.5px;
            font-weight: bold;
        }

        .meta-sub {
            margin-top: 3px;
            color: #6b7264;
            font-family: 'Courier New', Courier, monospace;
            font-size: 8.5px;
        }

        .meta-period {
            color: #1e3a2b;
            font-family: 'Courier New', Courier, monospace;
            font-size: 9px;
            font-weight: bold;
        }

        /* ---------------------------------------------------------
           SUMMARY RIBBON — one hairline-bordered strip, columns
           separated by vertical hairlines only, no individual boxes
        --------------------------------------------------------- */

        .summary-ribbon {
            border-top: 0.75px solid #1e3a2b;
            border-bottom: 0.75px solid #1e3a2b;
            margin-bottom: 30px;
        }

        .summary-cell {
            width: 25%;
            padding: 12px 16px;
            border-right: 0.5px solid #d8d5c8;
            vertical-align: top;
        }

        .summary-cell.last {
            border-right: none;
        }

        .summary-cell.featured {
            border-bottom: 2.5px solid {{ $closingColor }};
        }

        .summary-label {
            color: #8a9086;
            font-size: 7px;
            font-weight: bold;
            letter-spacing: 0.9px;
            text-transform: uppercase;
        }

        .summary-value {
            margin-top: 8px;
            font-family: 'Courier New', Courier, monospace;
            font-size: 13px;
            font-weight: bold;
            white-space: nowrap;
        }

        .summary-status {
            margin-top: 3px;
            font-size: 7px;
            letter-spacing: 0.3px;
        }

        /* ---------------------------------------------------------
           SECTION LABEL
        --------------------------------------------------------- */

        .section-title {
            color: #1e3a2b;
            font-size: 8.5px;
            font-weight: bold;
            letter-spacing: 1.4px;
            text-transform: uppercase;
            padding-bottom: 6px;
            border-bottom: 1.25px solid #1e3a2b;
            margin-bottom: 2px;
        }

        /* ---------------------------------------------------------
           LEDGER — refined header (no dark fill block), generous
           row padding, hairline rules only, no zebra striping
        --------------------------------------------------------- */

        .ledger thead th {
            padding: 9px 8px 7px 8px;
            border-bottom: 1.25px solid #1e3a2b;
            color: #6b7264;
            font-size: 7px;
            font-weight: bold;
            letter-spacing: 0.9px;
            text-transform: uppercase;
            text-align: left;
        }

        .ledger tbody td {
            padding: 8px;
            border-bottom: 0.5px solid #e5e2d6;
            font-size: 8.5px;
            vertical-align: middle;
        }

        .ledger tbody tr {
            page-break-inside: avoid;
        }

        .ledger .opening-row td {
            padding-top: 9px;
            padding-bottom: 9px;
            border-top: 0.75px solid #1e3a2b;
            border-bottom: 0.75px solid #1e3a2b;
            font-weight: bold;
            font-style: italic;
            color: #6b7264;
        }

        .ledger .date {
            color: #8a9086;
            font-family: 'Courier New', Courier, monospace;
            white-space: nowrap;
        }

        .ledger .description {
            color: #334155;
        }

        .ledger .reference {
            color: #8a9086;
            font-family: 'Courier New', Courier, monospace;
        }

        .amount {
            font-family: 'Courier New', Courier, monospace;
            font-weight: bold;
            white-space: nowrap;
        }

        /* Debit column = payments -> reduces what's owed -> forest */
        .debit {
            color: #1e3a2b;
        }

        /* Credit column = purchase invoices -> increases what's owed -> tag-red */
        .credit {
            color: #b23a2e;
        }

        .balance-cr {
            color: #b23a2e;
        }

        .balance-dr {
            color: #1e3a2b;
        }

        .empty-state {
            padding: 26px 10px !important;
            color: #b0b5a8;
            text-align: center;
            font-size: 8.5px !important;
            font-style: italic;
        }

        /* ---------------------------------------------------------
           TOTALS — double rule like a classic ledger sum, no fill
        --------------------------------------------------------- */

        .ledger tfoot td {
            padding: 11px 8px;
            border-top: 1.75px double #1e3a2b;
            font-size: 9px;
            font-weight: bold;
        }

        .totals-label {
            color: #1e3a2b;
            font-size: 8px;
            letter-spacing: 0.8px;
            text-transform: uppercase;
        }

        /* ---------------------------------------------------------
           AMOUNT DUE LINE — invoice-style bold statement, not a card
        --------------------------------------------------------- */

        .due-block {
            margin-top: 26px;
            padding-top: 14px;
            border-top: 0.75px solid #d8d5c8;
        }

        .due-message {
            color: #6b7264;
            font-size: 9px;
        }

        .due-amount-row {
            margin-top: 6px;
        }

        .due-amount-label {
            color: #8a9086;
            font-size: 7.5px;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .due-amount {
            color: {{ $closingColor }};
            font-family: 'Courier New', Courier, monospace;
            font-size: 20px;
            font-weight: bold;
            letter-spacing: -0.3px;
        }

        /* ---------------------------------------------------------
           FOOTER — fixed, repeats every page, with page numbers
        --------------------------------------------------------- */

        .footer {
            position: fixed;
            bottom: -36px;
            left: 0;
            right: 0;
            width: 100%;
            padding-top: 9px;
            border-top: 0.5px solid #d8d5c8;
            color: #b0b5a8;
            font-size: 7px;
        }

        .footer strong {
            color: #8a9086;
        }

        .legend-cr {
            color: #b23a2e;
            font-weight: bold;
        }

        .legend-dr {
            color: #1e3a2b;
            font-weight: bold;
        }

        .footer-note {
            margin-top: 4px;
            color: #d3d0c3;
            font-size: 6.5px;
        }

        .page-number:after {
            content: counter(page) " / " counter(pages);
        }
    </style>
</head>

<body>

    {{-- =========================================================
         MASTHEAD RULE
    ========================================================== --}}
    <div class="masthead-rule-heavy"></div>
    <div class="masthead-rule-light"></div>

    {{-- =========================================================
         HEADER
    ========================================================== --}}

    <table cellpadding="0" cellspacing="0">
        <tr>
            <td width="58%" style="vertical-align: top;">
                <div class="brand-name">{{ $shopName }}</div>
                <div class="brand-tagline">General Store &amp; Wholesale Supply</div>
                <div class="brand-details">
                    {{ $shopAddress }}<br>
                    {{ $shopPhone }}
                </div>
            </td>

            <td width="42%" class="text-right" style="vertical-align: top;">
                <div class="doc-label">Financial Statement</div>
                <div class="doc-title">Supplier Ledger</div>
                <div class="doc-ref">{{ $statementRef }}</div>
            </td>
        </tr>
    </table>

    {{-- =========================================================
         STATEMENT META
    ========================================================== --}}

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
                    {{ \Carbon\Carbon::parse($fromDate)->format('d M Y') }}
                    &nbsp;&ndash;&nbsp;
                    {{ \Carbon\Carbon::parse($toDate)->format('d M Y') }}
                </div>
            </td>
            <td width="25%" class="text-right" style="vertical-align: top;">
                <div class="meta-label">Generated</div>
                <div class="meta-sub" style="margin-top: 5px;">{{ now()->format('d M Y, h:i A') }}</div>
            </td>
        </tr>
    </table>

    {{-- =========================================================
         SUMMARY RIBBON
    ========================================================== --}}

    <table class="summary-ribbon" cellpadding="0" cellspacing="0">
        <tr>
            <td class="summary-cell">
                <div class="summary-label">Opening Balance</div>
                <div class="summary-value" style="color: {{ $openingColor }};">
                    Rs {{ number_format(abs($openingBalance), 2) }}
                    <span style="font-size: 8px;">{{ $openingLabel }}</span>
                </div>
                <div class="summary-status muted">
                    As of {{ \Carbon\Carbon::parse($fromDate)->format('d M Y') }}
                </div>
            </td>

            <td class="summary-cell">
                <div class="summary-label">Total Debit</div>
                <div class="summary-value forest">Rs {{ number_format($totalDebit, 2) }}</div>
                <div class="summary-status muted">Payments received by supplier</div>
            </td>

            <td class="summary-cell">
                <div class="summary-label">Total Credit</div>
                <div class="summary-value tag-red">Rs {{ number_format($totalCredit, 2) }}</div>
                <div class="summary-status muted">Purchases invoiced</div>
            </td>

            <td class="summary-cell last featured">
                <div class="summary-label">Closing Balance</div>
                <div class="summary-value" style="color: {{ $closingColor }};">
                    Rs {{ number_format(abs($closingBalance), 2) }}
                    <span style="font-size: 8px;">{{ $closingLabel }}</span>
                </div>
                <div class="summary-status" style="color: {{ $closingColor }};">
                    {{ $closingBalance >= 0 ? 'Amount Payable' : 'Advance Balance' }}
                </div>
            </td>
        </tr>
    </table>

    {{-- =========================================================
         LEDGER SECTION
    ========================================================== --}}

    <div class="section-title">Transaction History</div>

    <table class="ledger" cellpadding="0" cellspacing="0">

        <thead>
            <tr>
                <th width="13%">Date</th>
                <th width="35%">Description</th>
                <th width="14%">Reference</th>
                <th width="13%" class="text-right">Debit</th>
                <th width="13%" class="text-right">Credit</th>
                <th width="12%" class="text-right">Balance</th>
            </tr>
        </thead>

        <tbody>

            <tr class="opening-row">
                <td class="date">{{ \Carbon\Carbon::parse($fromDate)->format('d/m/Y') }}</td>
                <td colspan="4">Opening Balance Brought Forward</td>
                <td class="text-right amount {{ $openingBalance >= 0 ? 'balance-cr' : 'balance-dr' }}">
                    Rs {{ number_format(abs($openingBalance), 2) }} {{ $openingLabel }}
                </td>
            </tr>

            @forelse ($entries as $entry)
                <tr>
                    <td class="date">{{ \Carbon\Carbon::parse($entry['date'])->format('d/m/Y') }}</td>
                    <td class="description">{{ $entry['description'] }}</td>
                    <td class="reference">{{ $entry['reference'] ?? '—' }}</td>
                    <td class="text-right amount debit">
                        @if ($entry['type'] === 'debit')
                            Rs {{ number_format($entry['amount'], 2) }}
                        @else
                            &ndash;
                        @endif
                    </td>
                    <td class="text-right amount credit">
                        @if ($entry['type'] === 'credit')
                            Rs {{ number_format($entry['amount'], 2) }}
                        @else
                            &ndash;
                        @endif
                    </td>
                    <td class="text-right amount {{ $entry['balance'] >= 0 ? 'balance-cr' : 'balance-dr' }}">
                        Rs {{ number_format(abs($entry['balance']), 2) }} {{ $entry['balance'] >= 0 ? 'Cr' : 'Dr' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="empty-state">
                        No transactions were recorded during this reporting period.
                    </td>
                </tr>
            @endforelse

        </tbody>

        <tfoot>
            <tr>
                <td colspan="3" class="totals-label">Period Totals</td>
                <td class="text-right amount debit">Rs {{ number_format($totalDebit, 2) }}</td>
                <td class="text-right amount credit">Rs {{ number_format($totalCredit, 2) }}</td>
                <td class="text-right amount {{ $closingBalance >= 0 ? 'balance-cr' : 'balance-dr' }}">
                    Rs {{ number_format(abs($closingBalance), 2) }} {{ $closingLabel }}
                </td>
            </tr>
        </tfoot>

    </table>

    {{-- =========================================================
         AMOUNT DUE LINE
    ========================================================== --}}

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
                <div class="due-amount-row">
                    <div class="due-amount-label">Closing Balance</div>
                    <div class="due-amount">
                        Rs {{ number_format(abs($closingBalance), 2) }} {{ $closingLabel }}
                    </div>
                </div>
            </td>
        </tr>
    </table>

    {{-- =========================================================
         FOOTER
    ========================================================== --}}

    <div class="footer">
        <table cellpadding="0" cellspacing="0">
            <tr>
                <td width="55%" style="vertical-align: top;">
                    <strong>Legend:</strong>
                    <span class="legend-cr">Cr</span> Amount payable to supplier
                    &nbsp;&nbsp;&middot;&nbsp;&nbsp;
                    <span class="legend-dr">Dr</span> Advance / credit balance
                </td>
                <td width="25%" class="text-right" style="vertical-align: top;">
                    {{ $shopName }}
                </td>
                <td width="20%" class="text-right" style="vertical-align: top;">
                    Page <span class="page-number"></span>
                </td>
            </tr>
            <tr>
                <td colspan="3" class="footer-note">
                    This statement is computer-generated and does not require a signature.
                </td>
            </tr>
        </table>
    </div>

</body>

</html>
