<?php

namespace App\Services;

use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use Carbon\Carbon;

class SupplierStatementService
{
    public function build(Supplier $supplier, Carbon $from, Carbon $to): array
    {
        $openingPurchases = Purchase::where('supplier_id', $supplier->id)
            ->whereDate('date', '<', $from)
            ->sum('total_amount');

        $openingPaymentsDebit = SupplierPayment::where('supplier_id', $supplier->id)
            ->whereDate('date', '<', $from)
            ->where('type', 'debit')
            ->sum('amount');

        $openingPaymentsCredit = SupplierPayment::where('supplier_id', $supplier->id)
            ->whereDate('date', '<', $from)
            ->where('type', 'credit')
            ->sum('amount');

        $openingBalance = (float) $supplier->opening_balance
            + (float) $openingPurchases
            - (float) $openingPaymentsDebit
            + (float) $openingPaymentsCredit;

        $purchaseEntries = Purchase::where('supplier_id', $supplier->id)
            ->whereBetween('date', [$from, $to])
            ->get()
            ->map(fn($purchase) => [
                'date'        => $purchase->date,
                'sort_key'    => $purchase->created_at,
                'type'        => 'credit',
                'description' => 'Purchase Invoice',
                'reference'   => $purchase->invoice_number,
                'amount'      => (float) $purchase->total_amount,
            ]);

        $paymentEntries = SupplierPayment::where('supplier_id', $supplier->id)
            ->whereBetween('date', [$from, $to])
            ->with('purchase:id,invoice_number')
            ->get()
            ->map(function ($payment) {
                if ($payment->notes) {
                    $description = $payment->notes;
                } elseif ($payment->purchase_id) {
                    $description = 'Payment (Purchase)';
                } else {
                    $description = 'Direct Payment';
                }

                return [
                    'date'        => $payment->date,
                    'sort_key'    => $payment->created_at,
                    'type'        => $payment->type, // debit or credit, as stored
                    'description' => $description,
                    'reference'   => $payment->reference_no ?: ($payment->purchase->invoice_number ?? null),
                    'amount'      => (float) $payment->amount,
                ];
            });

        $entries = $purchaseEntries
            ->concat($paymentEntries)
            ->sortBy([
                fn($a, $b) => Carbon::parse($a['date'])->timestamp <=> Carbon::parse($b['date'])->timestamp,
                fn($a, $b) => Carbon::parse($a['sort_key'])->timestamp <=> Carbon::parse($b['sort_key'])->timestamp,
            ])
            ->values();

        $runningBalance = $openingBalance;
        $entries = $entries->map(function ($entry) use (&$runningBalance) {
            $runningBalance += $entry['type'] === 'credit' ? $entry['amount'] : -$entry['amount'];
            $entry['balance'] = $runningBalance;
            return $entry;
        });

        $totalDebit = $entries->where('type', 'debit')->sum('amount');
        $totalCredit = $entries->where('type', 'credit')->sum('amount');

        return [
            'entries'        => $entries,
            'openingBalance' => $openingBalance,
            'closingBalance' => $runningBalance,
            'totalDebit'     => $totalDebit,
            'totalCredit'    => $totalCredit,
        ];
    }
}
