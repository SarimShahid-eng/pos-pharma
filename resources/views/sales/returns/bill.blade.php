<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sale Return - {{ $saleReturn->sale->invoice_number ?? 'N/A' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-paper text-ink font-sans antialiased">

    <div x-data="billSwitcher()" class="min-h-screen p-5 md:p-8">
        <div class="max-w-[900px] mx-auto space-y-6">

            <!-- Screen-only toolbar -->
            <div class="print:hidden flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-muted">Point of Sale</span>
                    <h1 class="font-display text-2xl font-bold text-forest">
                        Sale Return — <span class="font-mono">{{ $saleReturn->sale->invoice_number ?? 'N/A' }}</span>
                    </h1>
                </div>

                <div class="flex flex-wrap items-center gap-2.5">
                    <!-- Format toggle -->
                    <div class="inline-flex rounded-[var(--radius-s)] border border-line bg-white p-1">
                        <button type="button" @click="format = 'thermal'"
                            :class="format === 'thermal' ? 'bg-forest text-white' : 'text-ink hover:bg-paper'"
                            class="rounded-[calc(var(--radius-s)-2px)] px-3 py-1.5 text-xs font-semibold transition-colors">
                            Thermal (80mm)
                        </button>
                    </div>

                    <button type="button" @click="window.print()"
                        class="inline-flex cursor-pointer items-center gap-2 rounded-[var(--radius-s)] bg-forest px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-forest-light transition-all">
                        <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2M6 14h12v8H6v-8z" />
                        </svg>
                        Print Receipt
                    </button>

                    <a href="{{ route('sales.returns.create') }}"
                        class="inline-flex items-center gap-2 rounded-[var(--radius-s)] bg-white border border-line px-3.5 py-2 text-xs font-semibold text-ink hover:bg-paper transition-colors">
                        + New Return
                    </a>
                    <a href="{{ route('sales.returns.index') }}"
                        class="inline-flex items-center gap-2 rounded-[var(--radius-s)] bg-white border border-line px-3.5 py-2 text-xs font-semibold text-ink hover:bg-paper transition-colors">
                        Back to Returns
                    </a>
                </div>
            </div>

            <!-- Thermal Receipt -->
            <div x-show="format === 'thermal'" id="bill-thermal-wrap">
                @include('sales.returns.partials.bill-thermal', ['saleReturn' => $saleReturn])
            </div>

        </div>
    </div>

    <style>
        @media print {
            @page {
                margin: 0;
            }

            body {
                background: #fff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('billSwitcher', () => ({
                format: 'thermal',
            }));
        });
    </script>
</body>

</html>
