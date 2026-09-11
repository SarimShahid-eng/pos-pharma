<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleReturn;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;


class SalesReportController extends Controller
{
    public function index(Request $request)
    {
        // dd($request->all());
        return view('reports.sales', $this->buildData($request));
    }
 
    public function export(Request $request)
    {
        $data = $this->buildData($request);
 
        $pdf = Pdf::loadView('reports.sales-pdf', $data)->setPaper('a4');
 
        $filename = 'sales-report-' . $data['fromDate'] . '-to-' . $data['toDate'] . '.pdf';
 
        return $pdf->download($filename);
    }
 
    /**
     * Shared by both the screen view and the PDF export, so the two
     * can never show different numbers for the same date range.
     */
    private function buildData(Request $request): array
    {
        $from = $request->filled('from_date')
            ? Carbon::parse($request->from_date)->startOfDay()
            : now()->startOfDay();
 
        $to = $request->filled('to_date')
            ? Carbon::parse($request->to_date)->endOfDay()
            : now()->endOfDay();
 
        $sales = Sale::whereBetween('date', [$from, $to])
            ->orderBy('date')
            ->get();
 
        $saleReturns = SaleReturn::whereBetween('date', [$from, $to])
            ->with('sale:id,invoice_number')
            ->orderBy('date')
            ->get();
 
        $totalSalesGross = $sales->sum('total_amount');
        $totalSalesDiscount = $sales->sum('discount_amount');
        $totalSalesNet = $sales->sum('net_amount');
 
        $totalSaleReturnsValue = $saleReturns->sum('total_amount');
        $totalRefunded = $saleReturns->sum('refunded_amount');
 
        $netSales = $totalSalesNet - $totalRefunded;
 
        return [
            'fromDate'              => $from->format('Y-m-d'),
            'toDate'                => $to->format('Y-m-d'),
 
            'sales'                 => $sales,
            'totalSalesGross'       => $totalSalesGross,
            'totalSalesDiscount'    => $totalSalesDiscount,
            'totalSalesNet'         => $totalSalesNet, 
            'saleReturns'           => $saleReturns,
            'totalSaleReturnsValue' => $totalSaleReturnsValue,
            'totalRefunded'         => $totalRefunded,
 
            'netSales'              => $netSales,
        ];
    }
}
