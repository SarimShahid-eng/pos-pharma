<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SaleReturn;
use App\Models\SaleReturnItem;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProfitReportController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->filled('from_date')
            ? Carbon::parse($request->from_date)->startOfDay()
            : now()->startOfMonth();

        $to = $request->filled('to_date')
            ? Carbon::parse($request->to_date)->endOfDay()
            : now()->endOfDay();

        // ---------------------------------------------------------------
        // Sales & Sale Returns in range
        // ---------------------------------------------------------------
        $sales = Sale::whereBetween('date', [$from, $to])->orderByDesc('date')->get();
        $totalSalesGross = $sales->sum('total_amount');
        $totalSalesDiscount = $sales->sum('discount_amount');
        $totalSalesRevenue = $sales->sum('net_amount');

        $saleReturns = SaleReturn::whereBetween('date', [$from, $to])
            ->with('sale:id,invoice_number')
            ->orderByDesc('date')
            ->get();
        $totalSaleReturnsValue = $saleReturns->sum('total_amount');
        $totalRefunded = $saleReturns->sum('refunded_amount');

        // ---------------------------------------------------------------
        // Purchases & Purchase Returns in range
        // ---------------------------------------------------------------
        $purchases = Purchase::whereBetween('date', [$from, $to])
            ->with('supplier:id,name')
            ->orderByDesc('date')
            ->get();
        $totalPurchases = $purchases->sum('total_amount');

        $purchaseReturns = PurchaseReturn::whereBetween('date', [$from, $to])
            ->with('purchase:id,invoice_number')
            ->orderByDesc('date')
            ->get();
        $totalPurchaseReturns = $purchaseReturns->sum('received_amount');

        // ---------------------------------------------------------------
        // Product-wise profit breakdown
        //
        // Cost is estimated using each product's CURRENT cost_price —
        // this schema has no per-batch/historical cost tracking
        // (Product.cost_price is a single running field, updated on
        // every purchase), so this is a simplification, not exact
        // FIFO/weighted-average costing. Good enough for a working
        // profit estimate; flag to the business owner if precise
        // historical costing is ever needed.
        // ---------------------------------------------------------------
        $soldItems = SaleItem::whereHas('sale', fn($q) => $q->whereBetween('date', [$from, $to]))
            ->with('product')
            ->get();

        $returnedItems = SaleReturnItem::whereHas('saleReturn', fn($q) => $q->whereBetween('date', [$from, $to]))
            ->with('product')
            ->get();

        $productStats = [];

        $ensureRow = function ($productId, $product) use (&$productStats) {
            if (! isset($productStats[$productId])) {
                $productStats[$productId] = [
                    'product'        => $product,
                    'qty_sold'       => 0,
                    'revenue'        => 0,
                    'qty_returned'   => 0,
                    'returned_value' => 0,
                    'cost_price'     => $product->cost_price ?? 0,
                ];
            }
        };

        foreach ($soldItems as $item) {
            $ensureRow($item->product_id, $item->product);
            $productStats[$item->product_id]['qty_sold'] += $item->qty;
            $productStats[$item->product_id]['revenue'] += $item->amount;
        }

        foreach ($returnedItems as $item) {
            $ensureRow($item->product_id, $item->product);
            $productStats[$item->product_id]['qty_returned'] += $item->qty;
            $productStats[$item->product_id]['returned_value'] += $item->amount;
        }

        $productRows = collect($productStats)->map(function ($row) {
            $netQty = $row['qty_sold'] - $row['qty_returned'];
            $netRevenue = $row['revenue'] - $row['returned_value'];
            $cogs = $netQty * $row['cost_price'];
            $profit = $netRevenue - $cogs;
            $margin = $netRevenue > 0 ? ($profit / $netRevenue) * 100 : 0;

            return array_merge($row, [
                'net_qty'     => $netQty,
                'net_revenue' => $netRevenue,
                'cogs'        => $cogs,
                'profit'      => $profit,
                'margin'      => $margin,
            ]);
        })->sortByDesc('profit')->values();

        $totalCogs = $productRows->sum('cogs');
        $totalNetRevenue = $productRows->sum('net_revenue');
        $grossProfit = $totalNetRevenue - $totalCogs;
        $grossMargin = $totalNetRevenue > 0 ? ($grossProfit / $totalNetRevenue) * 100 : 0;

        return view('reports.profit', [
            'fromDate'              => $from->format('Y-m-d'),
            'toDate'                => $to->format('Y-m-d'),

            'sales'                 => $sales,
            'totalSalesGross'       => $totalSalesGross,
            'totalSalesDiscount'    => $totalSalesDiscount,
            'totalSalesRevenue'     => $totalSalesRevenue,

            'saleReturns'           => $saleReturns,
            'totalSaleReturnsValue' => $totalSaleReturnsValue,
            'totalRefunded'         => $totalRefunded,

            'purchases'             => $purchases,
            'totalPurchases'        => $totalPurchases,

            'purchaseReturns'       => $purchaseReturns,
            'totalPurchaseReturns'  => $totalPurchaseReturns,
            'netPurchases'          => $totalPurchases - $totalPurchaseReturns,

            'productRows'           => $productRows,
            'totalNetRevenue'       => $totalNetRevenue,
            'totalCogs'             => $totalCogs,
            'grossProfit'           => $grossProfit,
            'grossMargin'           => $grossMargin,
        ]);
    }
}
