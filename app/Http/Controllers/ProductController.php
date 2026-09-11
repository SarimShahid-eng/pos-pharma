<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductStoreRequest;
use App\Models\Product;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::withSum('purchaseItems as purchased_qty', 'qty')
            ->withSum('purchaseItems as bonus_qty', 'bonus_qty')
            ->withSum('purchaseReturnItems as purchase_returned_qty', 'qty')
            ->withSum('saleItems as sold_qty', 'qty')
            ->withSum('saleReturnItems as sale_returned_qty', 'qty')
            ->when(filled($request->search), function ($query) use ($request) {
                $search = trim($request->search);
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('barcode', 'like', "%{$search}%")
                        ->orWhere('label_title', 'like', "%{$search}%");
                });
            });

        if ($request->filled('export') && $request->export === 'csv') {
            // Return the StreamedResponse immediately to terminate the request
            return $this->exportCsv($query->get());
        }
        $products = $query->paginate(10)->withQueryString();
        return view('products.index', compact('products'));
    }

    protected function exportCsv($products)
    {
        $fileName = 'products_export_' . now()->format('Y_m_d_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($products) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF"); // UTF-8 BOM

            fputcsv($file, [
                'Product Code / Barcode',
                'Label Title',
                'Product Name',
                'Unit',
                'Cost Price (Rs)',
                'Sale Price (Rs)',
                'Discount (Rs)',
                'Profit (Rs)',
                'Stock Quantity',
                'Stock Status',
            ]);

            foreach ($products as $product) {
                $stockQty = (float) ($product->stock_quantity ?? $product->stock_qty ?? 0);

                fputcsv($file, [
                    $product->barcode,
                    $product->label_title ?? '',
                    $product->name,
                    strtoupper($product->unit ?? ''),
                    number_format((float) $product->cost_price, 2, '.', ''),
                    number_format((float) $product->sale_price, 2, '.', ''),
                    number_format((float) ($product->discount ?? 0), 2, '.', ''),
                    number_format((float) ($product->profit ?? 0), 2, '.', ''),
                    number_format($stockQty, 2, '.', ''),
                    $stockQty <= 5 ? 'Low Stock' : 'In Stock',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
    public function create()
    {
        return view('products.create');
    }
    public function store(ProductStoreRequest $request)
    {
        $isUpdate = filled($request->update_id);

        $validated = $request->validated();
        $stockQty = array_key_exists('stock_qty', $validated) && filled($validated['stock_qty']) ? $validated['stock_qty'] : 0;
        $validated['stock_qty'] = $stockQty;
        try {

            DB::transaction(function () use ($validated) {
                Product::updateOrCreate(
                    ['id' => $validated['update_id']],
                    $validated
                );
            });
            $message = $isUpdate ? 'updated' : 'created';

            return redirect()
                ->route('products.index')
                ->with('success', 'Product ' . $message . ' successfully.');
        } catch (\Exception $e) {
            dd($e->getMessage());
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create supplier. Please try again.']);
        }
    }
    public function generateBarcode(): JsonResponse
    {
        do {
            // Generates a random 12-digit base number (e.g., 200000000000 - 299999999999)
            $number = mt_rand(200000000000, 299999999999);

            // Calculate EAN-13 checksum digit
            $digits = str_split((string) $number);
            $sum = 0;
            foreach ($digits as $index => $digit) {
                $sum += ($index % 2 === 0) ? $digit : $digit * 3;
            }
            $checksum = (10 - ($sum % 10)) % 10;

            $barcode = $number . $checksum;
        } while (Product::where('barcode', $barcode)->exists());

        return response()->json([
            'barcode' => $barcode,
        ]);
    }
    public function edit(Product $product)
    {
        return view('products.create', compact('product'));
    }
}
