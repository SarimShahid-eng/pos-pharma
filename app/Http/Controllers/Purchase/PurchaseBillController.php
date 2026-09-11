<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\PurchaseReturn;

class PurchaseBillController extends Controller
{
    public function index($invoiceNumber)
    {
        $purchase = $this->findOrFailByInvoiceNumber($invoiceNumber);
        return view('purchases.bill', compact('purchase'));
    }
    public function findOrFailByInvoiceNumber($invoiceNumber)
    {
        $purchase = Purchase::where('invoice_number', $invoiceNumber)->firstOrFail();
        return $purchase;
    }
    public function receipt(PurchaseReturn $purchaseReturn)
    {
        $purchaseReturn->load([
            'purchaseReturnItems.product',
            'purchaseReturnItems.purchaseItem',
            'purchase',
        ]);

        return view('purchases.returns.bill', compact('purchaseReturn'));
        // return redirect()->route('under_development');
    }
}
