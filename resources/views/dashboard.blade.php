@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')

    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="mb-1.5 text-[11px] font-semibold uppercase tracking-wide text-wheat">Overview</p>
            <h1 class="font-display text-[26px] font-semibold text-forest md:text-[28px]">
                Good morning, {{ explode(' ', auth()->user()->name ?? 'Aisha')[0] }}.
            </h1>
            <p class="text-[13.5px] text-muted">Here's how the store is trading today.</p>
        </div>
        {{-- <a href="{{ url('/orders/create') }}" class="rounded-s bg-forest px-4.5 py-2.5 text-[13.5px] font-semibold text-white transition-colors hover:bg-forest-light">
            + New Order
        </a> --}}
    </div>

    {{-- KPI ticket cards --}}
    @php
        $stats = $stats ?? [
            ['label' => "Today's Sales", 'value' => '148,230', 'prefix' => 'Rs', 'change' => '', 'trend' => 'up'],
            ['label' => 'Total Products', 'value' => '312', 'prefix' => '', 'change' => '', 'trend' => 'up'],
            ['label' => 'Low Stock Items', 'value' => '17', 'prefix' => '', 'change' => '', 'trend' => 'down'],
            ['label' => 'Today Purchase', 'value' => '48', 'prefix' => 'Rs', 'change' => '', 'trend' => 'up'],
        ];
    @endphp

    <div class="mb-5 grid grid-cols-2 gap-4 lg:grid-cols-4">
        @foreach ($stats as $stat)
            <div class="ticket">
                <div class="ticket-stub"></div>
                <div class="flex-1 px-4.5 py-4">
                    <p class="mb-2 text-[12px] font-medium text-muted">{{ $stat['label'] }}</p>
                    <p class="mb-1.5 font-mono text-[24px] font-semibold text-ink">
                        @if ($stat['prefix'])
                            <span class="mr-0.5 text-[13px] font-medium text-muted">{{ $stat['prefix'] }}</span>
                        @endif{{ $stat['value'] }}
                    </p>

                </div>
            </div>
        @endforeach
    </div>

    <div class="mb-4 grid grid-cols-1 gap-4 xl:grid-cols-[2fr_1fr]">
        {{-- Sales chart --}}
        <!-- Move x-data to the parent section to scope it for both header and chart -->
        <section class="panel" x-data="{
            range: '7D',
            salesData: {{ Js::from($salesData ?? []) }},
            max: 1,
            width: 1000,
            height: 180,
            paddingTop: 10,
            hoveredPoint: null,

            updateMax() {
                if (!this.salesData || this.salesData.length === 0) {
                    this.max = 1;
                    return;
                }
                let values = this.salesData.map(item => parseFloat(item.value) || 0);
                let maxVal = Math.max(...values);
                this.max = maxVal > 0 ? maxVal * 1.1 : 1;
            },

            fetchData(newRange) {
                this.range = newRange;
                this.hoveredPoint = null;
                let url = '{{ route('dashboard.salesChartData') }}' + '?range=' + newRange;
                fetch(url)
                    .then(res => res.json())
                    .then(data => {
                        this.salesData = data;
                        this.updateMax();
                    });
            },

            get points() {
                if (!this.salesData || this.salesData.length === 0) return '0,190';
                const count = this.salesData.length;
                const xStep = count > 1 ? this.width / (count - 1) : this.width;

                return this.salesData.map((point, i) => {
                    const val = parseFloat(point.value) || 0;
                    const x = (i * xStep).toFixed(1);
                    const calculatedY = this.height - (val / this.max) * this.height + this.paddingTop;
                    const y = calculatedY.toFixed(1);
                    return `${x},${y}`;
                }).join(' ');
            }
        }" x-init="updateMax()">

            <div class="mb-5 flex items-start justify-between">
                <div>
                    <h2 class="font-display text-[16.5px] font-semibold text-forest">
                        Sales <span
                            x-text="range === '7D' ? 'this week' : (range === '30D' ? 'this month' : 'this quarter')"></span>
                    </h2>
                    <p class="text-[12px] text-muted">Revenue across all counters</p>
                </div>

                <div class="flex gap-1 rounded-s border border-line bg-paper p-0.5">
                    <button @click="fetchData('7D')"
                        :class="range === '7D' ? 'bg-white text-forest shadow-sm' : 'text-muted'"
                        class="rounded px-2.5 py-1 text-[11.5px] font-semibold transition-colors">7D</button>
                    <button @click="fetchData('30D')"
                        :class="range === '30D' ? 'bg-white text-forest shadow-sm' : 'text-muted'"
                        class="rounded px-2.5 py-1 text-[11.5px] font-semibold transition-colors">30D</button>
                    <button @click="fetchData('90D')"
                        :class="range === '90D' ? 'bg-white text-forest shadow-sm' : 'text-muted'"
                        class="rounded px-2.5 py-1 text-[11.5px] font-semibold transition-colors">90D</button>
                </div>
            </div>

            <!-- Chart Wrapper with Hover Detection -->
            <div class="relative w-full h-[200px]" @mouseleave="hoveredPoint = null">

                <!-- Hover Tooltip -->
                <template x-if="hoveredPoint">
                    <div class="absolute z-10 -top-6 rounded bg-forest px-2 py-1 text-[10.5px] font-mono text-white shadow transition-all duration-75 -translate-x-1/2 pointer-events-none"
                        :style="`left: ${hoveredPoint.xPercent}%`">
                        <span x-text="`${hoveredPoint.day}: ${hoveredPoint.value}k`"></span>
                    </div>
                </template>

                <!-- SVG Chart -->
                <svg viewBox="0 0 1000 200" class="absolute inset-0 w-full h-full" preserveAspectRatio="none">
                    <defs>
                        <linearGradient id="areaGradient" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="var(--color-forest)" stop-opacity="0.2" />
                            <stop offset="100%" stop-color="var(--color-forest)" stop-opacity="0.01" />
                        </linearGradient>
                    </defs>

                    <polygon :points="`${points} 1000,200 0,200`" fill="url(#areaGradient)" />
                    <polyline fill="none" stroke="var(--color-forest)" stroke-width="2.5" stroke-linecap="round"
                        stroke-linejoin="round" :points="points" />
                </svg>

                <!-- Hover Event Zones -->
                <div class="absolute inset-0 flex">
                    <template x-for="(point, i) in salesData" :key="i">
                        <div class="h-full flex-1 cursor-pointer"
                            @mouseenter="hoveredPoint = {
                         day: point.day,
                         value: point.value,
                         xPercent: ((i / (salesData.length - 1 || 1)) * 100).toFixed(1)
                     }">
                        </div>
                    </template>
                </div>
            </div>

            <div class="flex justify-between text-[11px] font-medium text-muted mt-2 border-t border-line pt-2">
                <template x-if="salesData.length > 0">
                    <span x-text="salesData[0].day"></span>
                </template>
                <template x-if="salesData.length > 2">
                    <span x-text="salesData[Math.floor(salesData.length / 2)].day"></span>
                </template>
                <template x-if="salesData.length > 1">
                    <span x-text="salesData[salesData.length - 1].day"></span>
                </template>
            </div>
        </section>

        {{-- Low stock --}}
        @if (@$lowStockProducts)

            <section class="panel">
                <div class="mb-5 flex items-start justify-between">
                    <div>
                        <h2 class="font-display text-[16.5px] font-semibold text-forest">5 Low stock</h2>
                        <p class="text-[12px] text-muted">Reorder soon</p>
                    </div>
                    {{-- <a href="{{ url('/inventory') }}"
                    class="border-b border-forest text-[12.5px] font-semibold text-forest">View all</a> --}}
                </div>
                <ul>
                    {{-- @dd($lowStockProducts) --}}
                    @foreach ($lowStockProducts->take(5) as $product)
                        <li class="flex items-center justify-between border-b border-line py-2.5 last:border-b-0 last:pb-0">
                            <div>
                                <p class="mb-0.5 text-[13px] font-medium">{{ $product->name }}</p>
                                <p class="font-mono text-[10.5px] text-muted">{{ $product->barcode }}</p>
                            </div>
                            <span class="pill pill-warn">{{ $product->stock_quantity }} left</span>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif
    </div>


    {{-- Recent orders --}}
    <section class="panel">
        <div class="mb-5 flex items-start justify-between">
            <div>
                <h2 class="font-display text-[16.5px] font-semibold text-forest">Recent orders</h2>
                <p class="text-[12px] text-muted">Latest transactions across the store</p>
            </div>
            <a href="{{ route('sales.index') }}" class="border-b border-forest text-[12.5px] font-semibold text-forest">View
                all</a>
        </div>



        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr>
                        <th
                            class="border-b border-line px-3 pb-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-muted">
                            Invoice number</th>
                        <th
                            class="border-b border-line px-3 pb-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-muted">
                            Date</th>
                        <th
                            class="border-b border-line px-3 pb-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-muted">
                            Items</th>
                        <th
                            class="border-b border-line px-3 pb-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-muted">
                            Total</th>

                    </tr>
                </thead>
                <tbody>
                    @foreach ($lastTenSales as $order)
                        <tr class="hover:bg-paper">
                            <td class="border-b border-line px-3 py-3.5 font-mono text-[12.5px]">{{ $order->id }}</td>
                            <td class="border-b border-line px-3 py-3.5 font-mono text-[12.5px]">
                                {{ $order->formatted_date}}</td>
                            <td class="border-b border-line px-3 py-3.5 font-mono text-[12.5px]">
                                {{ count($order->saleItems) }}
                            </td>
                            <td class="border-b border-line px-3 py-3.5 font-mono text-[12.5px]">Rs
                                {{ $order->net_amount }}
                            </td>
                            {{-- <td class="border-b border-line px-3 py-3.5">
                                <span class="pill {{ $pillClass }}">{{ $order['status'] }}</span>
                            </td> --}}
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

@endsection
