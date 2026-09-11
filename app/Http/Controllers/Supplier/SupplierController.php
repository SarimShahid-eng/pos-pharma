<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Requests\SupplierStoreRequest;
use App\Models\Supplier;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $suppliers = Supplier::query()
            ->when(filled($request->search), function ($query) use ($request) {
                $search = trim($request->search);
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('phone_number', 'like', "%{$search}%");
                });
            })
            ->paginate(10);
        return view('suppliers.index', compact('suppliers'));
    }
    public function create()
    {
        return view('suppliers.create');
    }
    public function store(SupplierStoreRequest $request)
    {
        $isUpdate = filled($request->update_id);
        $validated = $request->validated();
        try {

            DB::transaction(function () use ($validated) {
                Supplier::updateOrCreate(
                    ['id' => $validated['update_id']],
                    $validated
                );
            });
            $message = $isUpdate ? 'updated' : 'created';

            return redirect()
                ->route('suppliers.index')
                ->with('success', 'Supplier ' . $message . ' successfully.');
        } catch (\Exception $e) {
            dd($e->getMessage());
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create supplier. Please try again.']);
        }
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.create', compact('supplier'));
    }
    public function toggleStatus(Supplier $supplier, Request $request)
    {
        $validated = $request->validate([
            'is_active' => 'boolean'
        ]);
        $supplier->update(['is_active' => $validated['is_active']]);
        $activateOrDeactivated = $validated['is_active'] ? 'Activated' : 'Deactived';
        return response()->json([
            'success'  => true,
            'message'  => 'Supplier ' . $activateOrDeactivated . '  successfully.',
        ], 200);
    }
}
