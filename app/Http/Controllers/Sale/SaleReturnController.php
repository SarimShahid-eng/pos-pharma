<?php

namespace App\Http\Controllers\Sale;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaleReturnStoreRequest;
use App\Models\ProductHistory;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SaleReturn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleReturnController extends Controller
{
    public function index(Request $request)
    {
        $saleReturns = SaleReturn::with(['sale', 'saleReturnItems', 'saleReturnItems.product'])
            ->when(filled($request->search), function ($query) use ($request) {
                $search = trim($request->search);
                $query->where(function ($q) use ($search) {
                    $q->orWhereHas('sale', function ($q) use ($search) {
                        $q->where('invoice_number', 'like', "%{$search}%");
                        // ->orWhere('invoice_number', 'like', "%{$search}%")
                        // ->orWhereHas('supplier', function ($sq) use ($search) {
                        //     $sq->where('name', 'like', "%{$search}%");
                        // });
                    });
                });
            })
            ->when($request->filled(['from_date', 'to_date']), function ($q) use ($request) {
                $q->whereBetween('date', [
                    $request->input('from_date'),
                    $request->input('to_date')
                ]);
            })->paginate(10);
        // $suppliers = Supplier::where('is_active', true)->get(['id', 'name']);
        return view('sales.returns.index', compact('saleReturns'));

        // return view('sale.returns.index');
    }
    public function store(SaleReturnStoreRequest $request)
    {
        $validated = $request->validated();

        try {
            $saleReturn = DB::transaction(function () use ($request, $validated) {
                $totalReturnAmount = 0;

                // Always create a new row — each submission is its own
                // historical event, not an edit of a previous return.
                $saleReturn = SaleReturn::create([
                    'sale_id'         => $validated['sale_id'],
                    'date'            => $validated['date'],
                    'notes'           => $validated['notes'],
                    'total_amount'    => 0, // set below, from the server-calculated total
                    'refunded_amount' => 0,
                ]);

                foreach ($request->items as $itemData) {
                    $returnQty = (float) $itemData['return_qty'];
                    if ($returnQty <= 0) continue;

                    $saleItem = SaleItem::where('sale_id', $validated['sale_id'])
                        ->where('id', $itemData['sale_item_id'])
                        ->firstOrFail();

                    // Server-side guard: reject if this would return more than
                    // remains, accounting for everything already returned
                    // against this exact sale item across all prior returns.
                    $remaining = $saleItem->qty - $saleItem->already_returned_qty;
                    if ($returnQty > $remaining) {
                        throw new \RuntimeException(
                            "Return quantity for \"{$saleItem->product->name}\" exceeds the remaining returnable quantity ({$remaining})."
                        );
                    }

                    $costPerItem = $saleItem->after_discount_price;
                    $lineAmount = $returnQty * $costPerItem;
                    $totalReturnAmount += $lineAmount;

                    $saleReturn->saleReturnItems()->create([
                        'product_id'   => $saleItem->product_id,
                        'sale_item_id' => $saleItem->id,
                        'qty'          => $returnQty,
                        'rate'         => $costPerItem,
                        'amount'       => $lineAmount,
                    ]);

                    ProductHistory::create([
                        'product_id'     => $saleItem->product_id,
                        'qty'            => $returnQty,
                        'type'           => 'sale_return',
                        'reference_id'   => $saleReturn->id,
                        'reference_type' => SaleReturn::class,
                    ]);
                }

                if ($totalReturnAmount <= 0) {
                    throw new \RuntimeException('No valid return quantities were submitted.');
                }

                // Server-calculated total wins — never the client-submitted one.
                $saleReturn->update([
                    'total_amount'    => $totalReturnAmount,
                    'refunded_amount' => $validated['refunded_amount'] ?? $totalReturnAmount,
                ]);

                return $saleReturn;
            });

            session()->flash('success', 'Sale Returned successfully.');

            return response()->json([
                'success'  => true,
                'message'  => 'Sale Returned successfully.',
                'redirect' => route('sales.bill.saleReturn.receipt', $saleReturn->id),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process sale return: ' . $e->getMessage(),
            ], 422);
        }
    }
    public function create(Request $request)
    {
        $invoiceNumber = $request->input('invoiceNumber');
        $sale = Sale::with(['saleItems.product'])
            ->where('invoice_number', $invoiceNumber)
            ->first();

        if ($invoiceNumber && !$sale) {
            abort(404);
        }
        return view('sales.returns.create', compact('invoiceNumber'));
    }
    public function searchByInvoice($invoiceNumber)
    {
        $sale = Sale::with(['saleItems', 'saleItems.product'])
            ->where('invoice_number', $invoiceNumber)
            ->first();

        if (! $sale) {
            return response()->json([
                'success' => false,
                'message' => 'No Sale found with that invoice number.',
            ], 404);
        }

        return response()->json([
            'success'  => true,
            'sale' => $sale,
        ]);
        // $getAllPurchase = PurchaseReturn::where('invoice_number', $invoiceNumber)->firstOrFail();
    }
    public function returnDetails(SaleReturn $saleReturn)
    {
        $sale = $saleReturn->sale()
            ->with([
                'saleItems.product',
                'saleReturns.saleReturnItems.product',
            ])
            ->firstOrFail();

        return view('sales.returns.details', compact('sale', 'saleReturn'));
    }
}
