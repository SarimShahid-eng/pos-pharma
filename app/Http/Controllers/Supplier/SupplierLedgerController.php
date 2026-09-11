<?php

namespace App\Http\Controllers\Supplier;

use App\Models\Supplier;
use App\Services\SupplierStatementService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;



class SupplierLedgerController extends Controller
{
    public function __construct(private SupplierStatementService $statementService) {}
    public function index(Request $request)
    {
        $from = $request->filled('from_date')
            ? Carbon::parse($request->from_date)->startOfDay()
            : now()->startOfMonth();

        $to = $request->filled('to_date')
            ? Carbon::parse($request->to_date)->endOfDay()
            : now()->endOfDay();
        // : now()->endOfDay();
        if ($request->supplier_id) {
            // entries->openingBalance,closingBalance,totalDebit,totalCredit,entries


            $supplier = Supplier::findOrFail($request->supplier_id);
            $summary = $this->statementService->build($supplier, $from, $to);

            $openingBalance = $summary['openingBalance'];
            $runningBalance = $summary['closingBalance'];
            $totalDebit = $summary['totalDebit'];
            $totalCredit = $summary['totalCredit'];
            $entries = $summary['entries'];

            // if($r)
            // ---------------------------------------------------------------
            // Opening balance: net everything dated before the "from" date.
            // ---------------------------------------------------------------
            // $openingPurchases = Purchase::where('supplier_id', $supplier->id)
            //     ->whereDate('date', '<', $from)
            //     ->sum('total_amount');

            // $openingPaymentsDebit = SupplierPayment::where('supplier_id', $supplier->id)
            //     ->whereDate('date', '<', $from)
            //     ->where('type', 'debit')
            //     ->sum('amount');

            // $openingPaymentsCredit = SupplierPayment::where('supplier_id', $supplier->id)
            //     ->whereDate('date', '<', $from)
            //     ->where('type', 'credit')
            //     ->sum('amount');

            // $openingBalance = (float) $supplier->opening_balance
            //     + (float) $openingPurchases
            //     - (float) $openingPaymentsDebit
            //     + (float) $openingPaymentsCredit;

            // // ---------------------------------------------------------------
            // // Entries within the selected range
            // // ---------------------------------------------------------------
            // $purchaseEntries = Purchase::where('supplier_id', $supplier->id)
            //     ->whereBetween('date', [$from, $to])
            //     ->get()
            //     ->map(fn($purchase) => [
            //         'date'        => $purchase->date,
            //         'sort_key'    => $purchase->created_at,
            //         'type'        => 'credit',
            //         'description' => 'Purchase Invoice',
            //         'reference'   => $purchase->invoice_number,
            //         'amount'      => (float) $purchase->total_amount,
            //     ]);
            // // $returnEntries = PurchaseReturn::where('supplier_id', $supplier->id)
            // //     ->whereBetween('date', [$from, $to])
            // //     ->get()
            // //     ->map(fn($return) => [
            // //         'date'        => $return->date,
            // //         'sort_key'    => $return->created_at,
            // //         'type'        => 'debit', // Reduces balance
            // //         'description' => 'Purchase Return',
            // //         'reference'   =>  $return->purchase->reference_no,
            // //         'amount'      => (float) $return->total_amount,
            // //     ]);

            // $paymentEntries = SupplierPayment::where('supplier_id', $supplier->id)
            //     ->whereBetween('date', [$from, $to])
            //     ->with('purchase:id,invoice_number')
            //     ->get()
            //     ->map(function ($payment) {
            //         if ($payment->notes) {
            //             $description = $payment->notes;
            //         } elseif ($payment->purchase_id) {
            //             $description = 'Payment (Purchase)';
            //         } else {
            //             $description = 'Direct Payment';
            //         }

            //         return [
            //             'date'        => $payment->date,
            //             'sort_key'    => $payment->created_at,
            //             'type'        => $payment->type, // debit or credit, as stored
            //             'description' => $description,
            //             'reference'   => $payment->reference_no ?: ($payment->purchase->invoice_number ?? null),
            //             'amount'      => (float) $payment->amount,
            //         ];
            //     });

            // $entries = $purchaseEntries
            //     // ->concat($returnEntries)
            //     ->concat($paymentEntries)
            //     ->sortBy([
            //         fn($a, $b) => Carbon::parse($a['date'])->timestamp <=> Carbon::parse($b['date'])->timestamp,
            //         fn($a, $b) => Carbon::parse($a['sort_key'])->timestamp <=> Carbon::parse($b['sort_key'])->timestamp,
            //     ])
            //     ->values();

            // // ---------------------------------------------------------------
            // // Running balance
            // // ---------------------------------------------------------------
            // $runningBalance = $openingBalance;
            // $entries = $entries->map(function ($entry) use (&$runningBalance) {
            //     $runningBalance += $entry['type'] === 'credit' ? $entry['amount'] : -$entry['amount'];
            //     $entry['balance'] = $runningBalance;
            //     return $entry;
            // });

            $totalDebit = $entries->where('type', 'debit')->sum('amount');
            $totalCredit = $entries->where('type', 'credit')->sum('amount');
            if (filled($request->export) && $request->export === "pdf") {
                $pdf = Pdf::loadView('suppliers.ledger.pdf', [
                    'supplier'       => $supplier ?? '',
                    'entries'        => $entries ?? [],
                    'openingBalance' => $openingBalance ?? 0,
                    'closingBalance' => $runningBalance ?? 0,
                    'totalDebit'     => $totalDebit ?? 0,
                    'totalCredit'    => $totalCredit ?? 0,
                    'fromDate'       => $from->format('Y-m-d'),
                    'toDate'         => $to->format('Y-m-d'),
                ]);
                return $pdf->download($supplier->name . 'Ledger.pdf');
            }
        }


        return view('suppliers.ledger.index', [
            'suppliers' => Supplier::where('is_active', true)->get(),
            'supplier'       => $supplier ?? '',
            'entries'        => $entries ?? [],
            'openingBalance' => $openingBalance ?? 0,
            'closingBalance' => $runningBalance ?? 0,
            'totalDebit'     => $totalDebit ?? 0,
            'totalCredit'    => $totalCredit ?? 0,
            'fromDate'       => $from->format('Y-m-d'),
            'toDate'         => $to->format('Y-m-d'),
        ]);
    }
}
