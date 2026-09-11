<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Requests\PurchaseReturnStoreRequest;
use App\Http\Controllers\Controller;
use App\Models\ProductHistory;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\PurchaseReturn;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseReturnController extends Controller
{
    public function index(Request $request)
    {
        $purchaseReturns = PurchaseReturn::with(['purchase', 'purchaseReturnItems', 'purchaseReturnItems.product'])
            ->when(filled($request->search), function ($query) use ($request) {
                $search = trim($request->search);
                $query->where(function ($q) use ($search) {
                    $q->orWhereHas('purchase', function ($q) use ($search) {
                        $q->where('reference_number', 'like', "%{$search}%")
                            ->orWhere('invoice_number', 'like', "%{$search}%")
                            ->orWhereHas('supplier', function ($sq) use ($search) {
                                $sq->where('name', 'like', "%{$search}%");
                            });
                    });
                });
            })
            ->when($request->filled(['from_date', 'to_date']), function ($q) use ($request) {
                $q->whereBetween('date', [
                    $request->input('from_date'),
                    $request->input('to_date')
                ]);
            })->paginate(10);
        $suppliers = Supplier::where('is_active', true)->get(['id', 'name']);
        return view('purchases.returns.index', compact('purchaseReturns', 'suppliers'));
    }
    public function create(Request $request)
    {
        $invoiceNumber = $request->input('invoiceNumber');
        $purchase = Purchase::with(['supplier', 'purchaseItems.product'])
            ->where('invoice_number', $invoiceNumber)
            ->first();

        if ($invoiceNumber && !$purchase) {
            abort(404);
        }
        return view('purchases.returns.create', compact('invoiceNumber'));
    }
    public function searchByInvoice($invoiceNumber)
    {
        $purchase = Purchase::with(['supplier', 'purchaseItems.product'])
            ->where('invoice_number', $invoiceNumber)
            ->first();

        if (! $purchase) {
            return response()->json([
                'success' => false,
                'message' => 'No purchase found with that invoice number.',
            ], 404);
        }

        return response()->json([
            'success'  => true,
            'purchase' => $purchase,
        ]);
        // $getAllPurchase = PurchaseReturn::where('invoice_number', $invoiceNumber)->firstOrFail();
    }
    public function store(PurchaseReturnStoreRequest $request)
    {
        $validated = $request->validated();
        DB::transaction(function () use ($request, $validated) {
            $totalReturnAmount = 0;
            $purchase = Purchase::find($validated['purchase_id']);

            $purchaseReturn = PurchaseReturn::create(
                [
                    'purchase_id'  => $validated['purchase_id'],
                    'supplier_id'  => $purchase->supplier_id,
                    'date'         => $validated['date'],
                    'notes'        => $validated['reason'],
                    'received_amount' => $validated['received_amount'],
                    'total_amount' => 0, // Updated after looping
                ]
            );

            foreach ($request->items as $itemData) {
                $returnQty = (float) $itemData['return_qty'];
                if ($returnQty <= 0) continue;

                $purchaseItem = PurchaseItem::where('purchase_id', $validated['purchase_id'])
                    ->where('id', $itemData['purchase_item_id'])
                    ->firstOrFail();

                $netUnitCost = $purchaseItem->qty > 0
                    ? ($purchaseItem->subtotal_amount / $purchaseItem->qty)
                    : $purchaseItem->unit_cost;
                //    netUnitCost calculating amount according to purchaseItem price with discount
                $lineAmount = $returnQty * $netUnitCost;
                $totalReturnAmount += $lineAmount;

                // if ($purchaseItem->purchaseReturnItems) {
                //     $purchaseItem->purchaseReturnItems()->delete();
                // }

                $purchaseReturn->purchaseReturnItems()->create([
                    'product_id'        => $purchaseItem->product_id,
                    'purchase_item_id'  => $purchaseItem->id,
                    'qty'               => $returnQty,
                    'unit_cost'         => $netUnitCost,
                    'amount'            => $lineAmount,
                ]);

                ProductHistory::create([
                    'product_id'     => $purchaseItem->product_id,
                    'qty'            => -$returnQty,
                    'type'           => 'purchase_return',
                    'reference_id'   => $purchaseReturn->id,
                    'reference_type' => PurchaseReturn::class,
                ]);
            }

            $paymentAmount = $validated['received_amount'] ?? $totalReturnAmount;

            $payment = [
                'supplier_id'     => $purchase->supplier_id,
                'payment_method'  => 'cash',
                'amount'          => $paymentAmount,
                'purchase_id'     => $purchase->id,
                'reference_no'    => $purchase->reference_number,
                'date'            => $validated['date'],
                'type'            => 'debit',
                'notes'           => 'Purchase Return',
            ];

            SupplierPayment::updateOrCreate(
                ['purchase_id' => $purchase->id, 'notes' => 'Purchase Return'],
                $payment
            );

            $purchaseReturn->update(['total_amount' => $totalReturnAmount]);
        });

        return response()->json([
            'success'  => true,
            'message'  => 'Return recorded successfully.',
            'redirect' => route('purchases.index'), // or a dedicated return receipt route, if you build one like the sale return receipt
        ]);
    }
    public function returnDetails(PurchaseReturn $purchaseReturn)
    {
        $purchase = $purchaseReturn->purchase()
            ->with([
                'purchaseItems.product',
                'purchaseReturns.purchaseReturnItems.product',
                'supplier',
            ])
            ->firstOrFail();

        return view('purchases.returns.details', compact('purchase', 'purchaseReturn'));
    }

    // public function modalstore(PurchaseReturnStoreRequest $request)
    // {
    //     $validated = $request->validated();
    //     try {

    //         DB::transaction(function () use ($request, $validated) {
    //             $totalReturnAmount = 0;
    //             $purchase = Purchase::find($validated['purchase_id']);


    //             $purchaseReturn = PurchaseReturn::updateOrCreate(
    //                 [
    //                     'purchase_id' => $validated['purchase_id']
    //                 ],
    //                 [
    //                     'purchase_id' => $validated['purchase_id'],
    //                     'supplier_id' => $purchase->supplier_id,
    //                     'date'        => $validated['date'],
    //                     'notes'       => $validated['reason'],
    //                     'total_amount' => 0, // Updated after looping
    //                 ]
    //             );

    //             foreach ($request->items as $itemData) {
    //                 $returnQty = (float) $itemData['return_qty'];
    //                 if ($returnQty <= 0) continue;

    //                 // Fetch original purchase item to get exact purchase rate
    //                 $purchaseItem = PurchaseItem::where('purchase_id', $validated['purchase_id'])
    //                     ->where('id', $itemData['purchase_item_id'])
    //                     ->firstOrFail();

    //                 $costPerItem = $purchaseItem->unit_cost;
    //                 $lineAmount = $returnQty * $costPerItem;
    //                 $totalReturnAmount += $lineAmount;
    //                 if ($purchaseItem->purchaseReturnItems) {
    //                     $purchaseItem->purchaseReturnItems()->delete();
    //                 }
    //                 // 1. Create Purchase Return Item record
    //                 $purchaseReturn->purchaseReturnItems()->create([
    //                     'product_id' => $purchaseItem->product_id,
    //                     'purchase_item_id' => $purchaseItem->id,
    //                     'qty'        => $returnQty,
    //                     'unit_cost'       => $costPerItem,
    //                     'amount'     => $lineAmount,
    //                 ]);

    //                 // 2. Decrement physical stock in Products table
    //                 // Product::where('id', $purchaseItem->product_id)
    //                 //     ->decrement('stock_qty', $returnQty);

    //                 // 3. Record Product History delta
    //                 ProductHistory::create([
    //                     'product_id'     => $purchaseItem->product_id,
    //                     'qty'            => -$returnQty,
    //                     'type'           => 'purchase_return',
    //                     'reference_id'   => $purchaseReturn->id,
    //                     'reference_type' => PurchaseReturn::class,
    //                 ]);
    //             }
    //             $payment = [
    //                 'supplier_id' => $purchase->supplier_id,
    //                 'payment_method' => 'cash',
    //                 'amount' => $totalReturnAmount,
    //                 'purchase_id' => $purchase->id,
    //                 'reference_no' => $purchase->reference_number,
    //                 'date' => $validated['date'],
    //                 'type' => 'debit',
    //                 'notes' => "Purchase Return",
    //             ];
    //             // Update parent PurchaseReturn total
    //             $purchaseReturn->update(['total_amount' => $totalReturnAmount]);

    //             // Adjust Supplier's ledger / current_balance (e.g., reduce liability owed to supplier)
    //             // $purchase->supplier->decrement('current_balance', $totalReturnAmount);
    //         });
    //         // $message = $isUpdate ? 'updated' : 'created';

    //         session()->flash('success', 'Purchase Returned successfully.');

    //         return response()->json([
    //             'success'  => true,
    //             'message'  => 'Purchase Returned successfully.',
    //             'redirect' => route('purchases.index')
    //         ], 200);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to process purchase return: ' . $e->getMessage()
    //         ], 422);
    //     }
    //     // dd($request->validated());
    // }
}
