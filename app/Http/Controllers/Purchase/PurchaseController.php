<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Requests\PurchaseStoreRequest;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use App\Services\NumberGenerator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $purchases = Purchase::with(['supplierPayment', 'purchaseItems', 'purchaseReturn', 'purchaseItems.product'])
            ->when(filled($request->search), function ($query) use ($request) {
                $search = trim($request->search);
                $query->where(function ($q) use ($search) {
                    $q->where('invoice_number', 'like', "%{$search}%")
                        ->orWhere('reference_number', 'like', "%{$search}%")
                        ->orWhereHas('supplier', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        })
                    ;
                });
            })
            ->when($request->filled('supplier_id'), function ($q) use ($request) {
                $supplierId = $request->input('supplier_id');
                $q->where('supplier_id', $supplierId);
            })
            ->when($request->filled(['from_date', 'to_date']), function ($q) use ($request) {
                $q->whereBetween('date', [
                    $request->input('from_date'),
                    $request->input('to_date')
                ]);
            })
            ->paginate(10);
        $suppliers = Supplier::where('is_active', true)->get(['id', 'name']);
        return view('purchases.index', compact('purchases', 'suppliers'));
    }
    public function create()
    {
        $generatedInvoiceNumber = NumberGenerator::generate(
            modelClass: Purchase::class,
            column: 'invoice_number',
            prefix: 'PUR-'
        );
        $suppliers = Supplier::orderBy('name')->where('is_active', true)->get();
        $products = Product::orderBy('name')->get();
        return view('purchases.create', compact('generatedInvoiceNumber', 'suppliers', 'products'));
    }
    public function store(PurchaseStoreRequest $request)
    {
        $isUpdate = filled($request->update_id);
        $validated = $request->validated();
        // dd($validated);
        try {

            DB::transaction(function () use ($validated, $isUpdate) {
                $purchase = Purchase::updateOrCreate(
                    ['id' => $validated['update_id']],
                    $validated
                );
                $purchase->purchaseItems()->delete();
                $purchase->purchaseItems()->createMany($validated['items']);
                $payment = [
                    'supplier_id' => $validated['supplier_id'],
                    'payment_method' => 'cash',
                    'amount' => $validated['paid_amount'],
                    'purchase_id' => $purchase->id,
                    'reference_no' => $purchase->reference_number,
                    'date' => $validated['date'],
                    'type' => 'debit',
                    'notes' => $validated['notes'],
                ];
                SupplierPayment::updateOrCreate([
                    'purchase_id' => $purchase->id,
                ], $payment);
            });
            $message = $isUpdate ? 'updated' : 'created';

            return redirect()
                ->route('purchases.index')
                ->with('success', 'Purchase ' . $message . ' successfully.');
        } catch (\Exception $e) {
            dd($e->getMessage());
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create purchase. Please try again.']);
        }
    }

    public function edit(Purchase $purchase)
    {
        // u742548502_tawakul_mart
        // pass:$CD78Ja5
        $suppliers = Supplier::orderBy('name')->where('is_active', true)->get();
        $products = Product::orderBy('name')->get();
        $purchaseItems = $purchase->purchaseItems->map(fn($item) => [
            'product_id' => $item->product_id,
            'qty'        => $item->qty,
            'unit_cost'  => $item->unit_cost,
            'bonus_qty'  => $item->bonus_qty,
            'discount_amount'  => $item->discount_amount,
            'subtotal_amount'  => $item->subtotal_amount,
        ]);
        return view('purchases.create', compact('purchase', 'suppliers', 'products', 'purchaseItems'));
    }

}
