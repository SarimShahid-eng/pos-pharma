<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Models\Sale;
use App\Models\SaleReturn;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $threshold = 30;
        $lowStockProducts = Product::query()
            ->withSum('purchaseItems as purchased_qty', 'qty')
            ->withSum('purchaseItems as bonus_qty', 'bonus_qty')
            ->withSum('purchaseReturnItems as purchase_returned_qty', 'qty')
            ->withSum('saleItems as sold_qty', 'qty')
            ->withSum('saleReturnItems as sale_returned_qty', 'qty')
            ->get()
            ->filter(fn($product) => $product->isLowStock($threshold))
            ->values();
        // dd($lowStockProducts);
        $lowStockProductsCount = $lowStockProducts->count();
        $totalProductsCount = Product::count();
        $todaySalesAmount = Sale::todaySale()->sum('net_amount');
        $todaySaleReturnsAmount = SaleReturn::whereDate('date', now()->today())->sum('refunded_amount');
        $netTodaySales = $todaySalesAmount - $todaySaleReturnsAmount;

        $todayPurchaseAmount = Purchase::todayPurchase()->sum('total_amount');
        // dd($todayPurchaseAmount);
        $todayPurchaseReturnsAmount = PurchaseReturn::whereDate('date', now()->today())->sum('total_amount');
        $netTodayPurchases = $todayPurchaseAmount - $todayPurchaseReturnsAmount;

        $stats = [
            ['label' => "Today's Sales", 'value' => $netTodaySales, 'prefix' => 'Rs', 'change' => '+12.4%', 'trend' => 'up'],
            ['label' => 'Total Products', 'value' => $totalProductsCount, 'prefix' => '', 'change' => '+4.1%', 'trend' => 'up'],
            ['label' => 'Low Stock Items', 'value' => $lowStockProductsCount, 'prefix' => '', 'change' => '+5', 'trend' => 'down'],
            ['label' => 'Today Purchase', 'value' => $netTodayPurchases, 'prefix' => 'Rs', 'change' => '+9.8%', 'trend' => 'up'],
        ];
        $startDate = Carbon::now()->subDays(6)->toDateString();

        // Fetch total revenue grouped by your date column
        $sales = Sale::where('date', '>=', $startDate)
            ->selectRaw('DATE(date) as sale_date, SUM(total_amount) as total')
            ->groupBy('sale_date')
            ->pluck('total', 'sale_date');

        // Build complete 7-day dataset (fills missing days with 0)
        $salesData = collect();
        for ($i = 6; $i >= 0; $i--) {
            $currentDate = Carbon::now()->subDays($i);
            $dateKey = $currentDate->toDateString();

            $rawTotal = $sales->get($dateKey, 0);

            $salesData->push([
                'day'   => $currentDate->format('D'), // Mon, Tue, etc.
                'value' => round($rawTotal / 1000, 1), // Converts total to thousands (e.g. 5200 -> 5.2k)
            ]);
        }

        // $salesData = [
        //     ['day' => 'Mon', 'value' => 62],
        //     ['day' => 'Tue', 'value' => 78],
        //     ['day' => 'Wed', 'value' => 55],
        //     ['day' => 'Thu', 'value' => 91],
        //     ['day' => 'Fri', 'value' => 108],
        //     ['day' => 'Sat', 'value' => 132],
        //     ['day' => 'Sun', 'value' => 97],
        // ];



        // $lowStock = [
        //     ['name' => 'Basmati Rice 5kg', 'sku' => 'GR-1042', 'left' => 4],
        //     ['name' => 'Olive Oil 1L', 'sku' => 'GR-2210', 'left' => 6],
        //     ['name' => 'Fresh Milk 1L', 'sku' => 'DA-0087', 'left' => 3],
        //     ['name' => 'Brown Eggs (dz)', 'sku' => 'DA-0142', 'left' => 8],
        // ];

        // $orders = [
        //     ['id' => '#10231', 'customer' => 'Bilal Ahmed', 'items' => 6, 'total' => '2,340', 'status' => 'Completed'],
        //     ['id' => '#10230', 'customer' => 'Sana Tariq', 'items' => 3, 'total' => '860', 'status' => 'Processing'],
        //     ['id' => '#10229', 'customer' => 'Usman Malik', 'items' => 11, 'total' => '5,120', 'status' => 'Completed'],
        //     ['id' => '#10228', 'customer' => 'Areeba Iqbal', 'items' => 2, 'total' => '410', 'status' => 'Cancelled'],
        //     ['id' => '#10227', 'customer' => 'Hamza Sheikh', 'items' => 7, 'total' => '1,975', 'status' => 'Processing'],
        // ];
        $lastTenSales = Sale::latest('id')
            ->limit(10)
            ->get();

        return view('dashboard', compact('stats', 'salesData', 'lowStockProducts', 'lastTenSales', 'salesData'));
    }
    public function salesChartData(Request $request)
    {
        $range = $request->get('range', '7D');

        $days = match ($range) {
            '30D' => 30,
            '90D' => 90,
            default => 7,
        };

        $startDate = Carbon::now()->subDays($days - 1)->toDateString();

        $sales = Sale::where('date', '>=', $startDate)
            ->selectRaw('DATE(date) as sale_date, SUM(total_amount) as total')
            ->groupBy('sale_date')
            ->pluck('total', 'sale_date');

        $salesData = collect();
        for ($i = $days - 1; $i >= 0; $i--) {
            $currentDate = Carbon::now()->subDays($i);
            $dateKey = $currentDate->toDateString();
            $rawTotal = $sales->get($dateKey, 0);

            $salesData->push([
                'day' => $days > 7 ? $currentDate->format('M d') : $currentDate->format('D'),
                'value' => round($rawTotal / 1000, 1),
            ]);
        }

        return response()->json($salesData);
    }
}
