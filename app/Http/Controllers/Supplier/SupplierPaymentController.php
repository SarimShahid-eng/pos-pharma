<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Requests\SupplierPaymentStoreRequest;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;


class SupplierPaymentController extends Controller
{
    public function index(Request $request)
    {
        $supplierPayments = SupplierPayment::query()
            ->when(filled($request->search), function ($query) use ($request) {
                $search = trim($request->search);
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('phone_number', 'like', "%{$search}%");
                });
            })
             ->when($request->filled('date'), function ($q) use ($request) {
                $date = $request->input('date');
                $q->whereDate('date', $date);
            })
            ->paginate(10);
        $suppliers = Supplier::orderBy('name')->where('is_active',true)->get(['id', 'name']);
        return view('supplierPayments.index', compact('supplierPayments', 'suppliers'));
    }
    public function create()
    {
        $suppliers = Supplier::orderBy('name')->where('is_active',true)->get(['id', 'name']);
        return view('supplierPayments.create', compact('suppliers'));
    }
    public function store(SupplierPaymentStoreRequest $request)
    {
        $isUpdate = filled($request->update_id);
        $validated = $request->validated();
        try {

            DB::transaction(function () use ($validated) {
                SupplierPayment::updateOrCreate(
                    ['id' => $validated['update_id']],
                    $validated
                );
            });
            $message = $isUpdate ? 'updated' : 'created';

            return redirect()
                ->route('supplierPayments.index')
                ->with('success', 'SupplierPayment ' . $message . ' successfully.');
        } catch (\Exception $e) {
            dd($e->getMessage());
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create supplierPayment. Please try again.']);
        }
    }

    public function edit(SupplierPayment $supplierPayment)
    {
        return view('supplierPayments.create', compact('supplierPayment'));
    }
}
