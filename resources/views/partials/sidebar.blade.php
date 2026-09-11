@php
    // Get current supplier ID if viewing a supplier page, or fall back to the first supplier in DB
    // $currentSupplierId = request()->route('supplier') ?? (\App\Models\Supplier::value('id'));

    $navGroups = [
        'Overview' => [
            [
                'label' => 'Dashboard',
                'icon' =>
                    '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>',
                'url' => route('dashboard'),
                'match' => 'dashboard',
            ],
        ],
        'Catalog' => [
            [
                'label' => 'Products',
                'icon' =>
                    '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>',
                'url' => route('products.index'),
                'match' => 'products.*',
            ],
        ],
        'Supplier' => [
            [
                'label' => 'Suppliers',
                'icon' =>
                    '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0h4m-4 0H7"/></svg>',
                'url' => route('suppliers.index'),
                'match' => ['suppliers.index', 'suppliers.create', 'suppliers.edit'],
            ],
            [
                'label' => 'Supplier Payments',
                'icon' =>
                    '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>',
                'url' => route('supplierPayments.index'),
                'match' => 'supplierPayments.*',
            ],
            [
                'label' => 'Ledger',
                'icon' =>
                    '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>',
                'url' => route('suppliers.ledger.index'),
                'match' => 'suppliers.ledger.*',
            ],
            [
                'label' => 'Invoicing',
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 0 1-2.25 2.25M16.5 7.5V18a2.25 2.25 0 0 0 2.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 0 0 2.25 2.25h13.5M6 7.5h3v3H6v-3Z" />
                            </svg>',
                'url' => route('suppliers.invoice.index'),
                'match' => 'suppliers.invoice.*',
            ],
        ],
        'Actions' => [
            [
                'label' => 'Purchase',
                'icon' =>
                    '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>',
                'url' => route('purchases.index'),
                'match' => ['purchases.index', 'purchases.create', 'purchases.edit'],
            ],
            [
                'label' => 'Purchase Returns',
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m15 15 6-6m0 0-6-6m6 6H9a6 6 0 0 0 0 12h3" />
                    </svg>',
                'url' => route('purchases.returns.index'),
                'match' => 'purchases.returns.*',
            ],
            [
                'label' => 'Sales',
                'icon' =>
                    '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>',
                'url' => route('sales.index'),
                'match' => ['sales.index', 'sales.create', 'sales.edit'],
            ],
            [
                'label' => 'Sales Return',
                'icon' =>
                    '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>',
                'url' => route('sales.returns.index'),
                'match' => 'sales.returns.*',
            ],
        ],
        'Reports' => [
            [
                'label' => 'Profit Report',
                'icon' =>
                    '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>',
                'url' => route('reports.profit.index'),
                'match' => 'reports.profit.*',
            ],
            [
                'label' => 'Sale Report',
                'icon' =>
                    '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
',
                'url' => route('reports.sales.index'),
                'match' => 'reports.sales.*',
            ],
            // [
            //     'label' => 'Purchase Report',
            //     'icon' =>
            //         '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>',
            //     'url' => route('under_development'),
            //     'match' => 'reports.purchase.*',
            // ],
        ],
    ];
@endphp

<aside x-data :class="$store.ui.sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
    class="fixed z-20 flex h-screen w-64 flex-shrink-0 flex-col bg-forest p-4 text-slate-200 transition-transform duration-200 md:sticky md:top-0 md:translate-x-0">
    <div class="flex items-center gap-2.5 px-2 pb-5 pt-1">
        <span
            class="flex h-9 w-9 items-center justify-center rounded-lg bg-wheat font-display text-[15px] font-bold text-forest">BPH</span>
        <div>
            <strong class="block font-display text-[16px] font-semibold text-white">BEHROZ</strong>
            <small class="text-[11px] uppercase tracking-wide text-forest-tint/60">Pharmacy</small>
        </div>
    </div>

    <nav class="flex-1 space-y-0.5 overflow-y-auto pt-1">
        @foreach ($navGroups as $group => $items)
            <p
                class="mb-1.5 mt-4 px-2.5 text-[10.5px] font-semibold uppercase tracking-wide text-forest-tint/50 first:mt-0">
                {{ $group }}
            </p>
            @foreach ($items as $item)
                <a href="{{ $item['url'] }}" class="nav-link {{ request()->routeIs($item['match']) ? 'active' : '' }}">
                    <span
                        class="inline-flex w-[18px] items-center justify-center text-current opacity-90">{!! $item['icon'] !!}</span>
                    {{ $item['label'] }}
                </a>
            @endforeach
        @endforeach
    </nav>
</aside>
