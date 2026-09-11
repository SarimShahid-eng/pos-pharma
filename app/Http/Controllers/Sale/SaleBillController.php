<?php

namespace App\Http\Controllers\Sale;

use App\Models\Sale;
use App\Models\SaleReturn;
use App\Http\Controllers\Controller;


class SaleBillController extends Controller
{
    public function index($invoiceNumber)
    {
        $sale = $this->findOrFailByInvoiceNumber($invoiceNumber);
        $sale->load('saleItems.product');

        return view('sales.bill', compact('sale'));
    }
    public function receipt(SaleReturn $saleReturn)
    {
        $saleReturn->load([
            'saleReturnItems.product',
            'saleReturnItems.saleItem',
            'sale',
        ]);

        return view('sales.returns.bill', compact('saleReturn'));
    }
    public function findOrFailByInvoiceNumber($invoiceNumber)
    {
        $sale = Sale::where('invoice_number', $invoiceNumber)->firstOrFail();
        return $sale;
    }
}
