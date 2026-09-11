<?php

namespace App\Http\Controllers\Supplier;

use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Models\Supplier;
use App\Services\SupplierStatementService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class SupplierInvoiceController extends Controller
{
    public function __construct(private SupplierStatementService $statementService)
    {
    }

    public function index(Request $request)
    {
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();

        return view('suppliers.invoice.index', array_merge(
            ['suppliers' => $suppliers],
            $this->buildData($request)
        ));
    }

    public function export(Request $request)
    {
        $data = $this->buildData($request);

        if (! $data['supplier']) {
            return back()->with('error', 'Select a supplier before exporting.');
        }

        $pdf = Pdf::loadView('suppliers.invoice.pdf', $data)->setPaper('a4');

        $filename = 'supplier-invoice-' . str($data['supplier']->name)->slug()
            . '-' . $data['fromDate'] . '-to-' . $data['toDate'] . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Shared by both the screen view and the PDF export, so the two
     * documents can never drift apart — same query, same balance math
     * (via SupplierStatementService, the same source the single-supplier
     * ledger already uses), same numbers, guaranteed.
     */
    private function buildData(Request $request): array
    {
        $from = $request->filled('from_date')
            ? Carbon::parse($request->from_date)->startOfDay()
            : now()->startOfMonth();

        $to = $request->filled('to_date')
            ? Carbon::parse($request->to_date)->endOfDay()
            : now()->endOfDay();

        $supplier = null;
        $purchases = collect();
        $purchaseReturns = collect();
        $openingBalance = 0;
        $closingBalance = 0;
        $totalDebit = 0;
        $totalCredit = 0;

        if ($request->filled('supplier_id')) {
            $supplier = Supplier::findOrFail($request->supplier_id);

            $purchases = Purchase::where('supplier_id', $supplier->id)
                ->whereBetween('date', [$from, $to])
                ->with('purchaseItems.product')
                ->orderBy('date')
                ->get();

            $purchaseReturns = PurchaseReturn::where('supplier_id', $supplier->id)
                ->whereBetween('date', [$from, $to])
                ->with('purchaseReturnItems.product', 'purchase:id,invoice_number')
                ->orderBy('date')
                ->get();

            $summary = $this->statementService->build($supplier, $from, $to);

            $openingBalance = $summary['openingBalance'];
            $closingBalance = $summary['closingBalance'];
            $totalDebit = $summary['totalDebit'];
            $totalCredit = $summary['totalCredit'];
        }

        return [
            'supplier'        => $supplier,
            'purchases'       => $purchases,
            'purchaseReturns' => $purchaseReturns,
            'openingBalance'  => $openingBalance,
            'closingBalance'  => $closingBalance,
            'totalDebit'      => $totalDebit,
            'totalCredit'     => $totalCredit,
            'fromDate'        => $from->format('Y-m-d'),
            'toDate'          => $to->format('Y-m-d'),
        ];
    }
}
