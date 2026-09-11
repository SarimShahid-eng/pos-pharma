<?php

namespace App\Http\Controllers\Sale;

use App\Http\Requests\SaleStoreRequest;
use App\Models\Product;
use App\Models\Sale;
use App\Services\NumberGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;


class SaleController extends Controller
{
    public function index(Request $request)
    {
        $sales = Sale::with(['saleItems', 'saleItems.product', 'saleReturn'])
            ->when(filled($request->search), function ($query) use ($request) {
                $search = trim($request->search);
                $query->where(function ($q) use ($search) {
                    $q->where('invoice_number', 'like', "%{$search}%");
                });
            })
            ->when(filled($request->payment_method), function ($q) use ($request) {
                $q->where('payment_method', $request->payment_method);
            })
            ->when($request->filled(['from_date', 'to_date']), function ($q) use ($request) {
                $q->whereBetween('date', [
                    $request->input('from_date'),
                    $request->input('to_date')
                ]);
            })
            ->paginate(10);
        return view('sales.index', compact('sales'));
    }
    public function create()
    {
        $generatedInvoiceNumber = NumberGenerator::generate(
            modelClass: Sale::class,
            column: 'invoice_number',
            prefix: 'SL-'
        );
        $products = Product::orderBy('name')->get();
        return view('sales.create', compact('products', 'generatedInvoiceNumber'));
    }
    public function store(SaleStoreRequest $request)
    {
        $validated = $request->validated();
        $isUpdate = filled($validated['update_id']);
        try {
            $sale = DB::transaction(function () use ($validated, $isUpdate) {
                $sale = Sale::updateOrCreate(
                    ['id' => $validated['update_id']],
                    $validated
                );
                if ($isUpdate) {
                    $sale->saleItems()->delete();
                }
                $sale->saleItems()->createMany($validated['items']);
                return $sale;
            });
            // $sale=Sale::
            // $message = $isUpdate ? 'updated' : 'created';
            return redirect()
                ->route('sales.bill.index', ['invoiceNumber' => $sale->invoice_number])
                ->with('success', 'Sale completed successfully.');
            // return redirect()
            //     ->route('sales.index')
            //     ->with('success', 'Sale ' . $message . ' successfully.');
        } catch (\Exception $e) {
            dd($e->getMessage());
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create sale. Please try again.']);
        }
    }
    public function edit(Sale $sale)
    {
        $products = Product::orderBy('name')->get();
        $saleItems = $sale->saleItems->map(fn($item) => [
            'product_id' => $item->product_id,
            'qty'        => $item->qty,
            'rate'  => $item->rate,
            'discount'  => $item->discount,
            'after_discount_price'  => $item->after_discount_price,
            'amount'  => $item->amount,
        ]);
        return view('sales.create', compact('sale', 'products', 'saleItems'));
    }
}
