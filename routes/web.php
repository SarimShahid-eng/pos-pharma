<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfitController;
use App\Http\Controllers\Purchase\PurchaseBillController;
use App\Http\Controllers\Purchase\PurchaseController;
use App\Http\Controllers\Purchase\PurchaseReturnController;
use App\Http\Controllers\Reports\ProfitReportController;
use App\Http\Controllers\Reports\SalesReportController;
use App\Http\Controllers\Sale\SaleBillController;
use App\Http\Controllers\Sale\SaleController;
use App\Http\Controllers\Sale\SaleReturnController;
use App\Http\Controllers\Supplier\SupplierController;
use App\Http\Controllers\Supplier\SupplierInvoiceController;
use App\Http\Controllers\Supplier\SupplierLedgerController;
use App\Http\Controllers\Supplier\SupplierPaymentController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
    // return view('welcome');
});
Route::get('optimize', function () {
    Artisan::call('optimize:clear');
    Artisan::call('optimize');

    return response()->json([
        'status' => 'success',
        'message' => 'Database refreshed, seeded, and application optimized successfully.',
    ]);
})->name('system.setup_db');
Route::middleware('auth')->group(function () {
    Route::get('under-development', function () {
        return view('under_development');
    })->name('under_development');
    Route::controller(DashboardController::class)
        // ->prefix('dashboard')
        ->group(function () {
            Route::get('dashboard', 'index')->name('dashboard');
            Route::prefix('dashboard')
                ->name('dashboard.')
                ->group(function () {
                    Route::get('salesChartData', 'salesChartData')->name('salesChartData');
                });
        });
    Route::controller(ProductController::class)
        ->name('products.')
        ->prefix('products')
        ->group(function () {
            Route::get('index', 'index')->name('index');
            Route::post('store', 'store')->name('store');
            Route::get('create', 'create')->name('create');
            Route::get('edit/{product}', 'edit')->name('edit');
            Route::get('generate-barcode', 'generateBarcode')->name('generate_barcode');
            // Route::get('export', 'generateBarcode')->name('generate_barcode');
            // Route::get('generate-barcode', 'generateBarcode')->name('generate_barcode');
        });
    Route::name('suppliers.')
        ->prefix('suppliers')
        ->group(function () {
            Route::controller(SupplierController::class)->group(function () {
                Route::get('index', 'index')->name('index');
                Route::post('store', 'store')->name('store');
                Route::get('create', 'create')->name('create');
                Route::get('edit/{supplier}', 'edit')->name('edit');
                Route::patch('toggle-status/{supplier}', 'toggleStatus')->name('toggle_status');
            });
            Route::name('ledger.')
                ->prefix('ledger')
                ->group(function () {
                    Route::controller(SupplierLedgerController::class)->group(function () {
                        Route::get('index', 'index')->name('index');
                        // Route::get('report/{supplier}', 'report')->name('report');
                        // Route::post('store', 'store')->name('store');
                        // Route::get('create', 'create')->name('create');
                        // Route::get('edit/{supplier}', 'edit')->name('edit');
                        // Route::patch('toggle-status/{supplier}', 'toggleStatus')->name('toggle_status');
                    });
                });
            Route::name('invoice.')
                ->prefix('invoice')
                ->group(function () {
                    Route::controller(SupplierInvoiceController::class)->group(function () {
                        Route::get('index', 'index')->name('index');
                        Route::get('export', 'export')->name('export');
                    });
                });
        });
    Route::controller(SupplierPaymentController::class)
        ->name('supplierPayments.')
        ->prefix('supplierPayments')
        ->group(function () {
            Route::get('index', 'index')->name('index');
            Route::post('store', 'store')->name('store');
            Route::get('create', 'create')->name('create');
            Route::get('edit/{supplierPayment}', 'edit')->name('edit');
        });
    Route::name('purchases.')
        ->prefix('purchases')
        ->group(function () {
            Route::controller(PurchaseController::class)
                ->group(function () {
                    Route::get('index', 'index')->name('index');
                    Route::post('store', 'store')->name('store');
                    Route::get('create', 'create')->name('create');
                    Route::get('edit/{purchase}', 'edit')->name('edit');
                });
            Route::controller(PurchaseReturnController::class)
                ->name('returns.')
                ->prefix('returns')
                ->group(function () {
                    Route::get('index', 'index')->name('index');
                    Route::post('store', 'store')->name('store');
                    Route::get('create', 'create')->name('create');
                    Route::get('search/{invoiceNumber}', 'searchByInvoice')->name('search');
                    Route::get('details/{purchaseReturn}', 'returnDetails')->name('details');
                });
            Route::controller(PurchaseBillController::class)
                ->name('bill.')
                ->prefix('bill')
                ->group(function () {
                    Route::get('purchase-returns/{purchaseReturn}/receipt', 'receipt')->name('purchaseReturn.receipt');
                    Route::get('index/{invoiceNumber}', 'index')->name('index');
                });
        });
    Route::name('sales.')
        ->prefix('sales')
        ->group(function () {
            Route::controller(SaleController::class)
                ->group(function () {
                    Route::get('index', 'index')->name('index');
                    Route::post('store', 'store')->name('store');
                    Route::get('create', 'create')->name('create');
                    Route::get('edit/{sale}', 'edit')->name('edit');
                });
            Route::controller(SaleReturnController::class)
                ->name('returns.')
                ->prefix('returns')
                ->group(function () {
                    Route::get('index', 'index')->name('index');
                    Route::post('store', 'store')->name('store');
                    Route::get('create', 'create')->name('create');
                    Route::get('edit/{sale}', 'edit')->name('edit');
                    Route::get('search/{invoiceNumber}', 'searchByInvoice')->name('search');
                    Route::get('details/{saleReturn}', 'returnDetails')->name('details');
                });
            Route::controller(SaleBillController::class)
                ->name('bill.')
                ->prefix('bill')
                ->group(function () {
                    Route::get('/sale-returns/{saleReturn}/receipt', 'receipt')->name('saleReturn.receipt');
                    Route::get('index/{invoiceNumber}', 'index')->name('index');
                    // Route::post('store', 'store')->name('store');
                    // Route::get('create', 'create')->name('create');
                    // Route::get('edit/{sale}', 'edit')->name('edit');
                });
        });
    Route::prefix('reports')
        ->name('reports.')
        ->group(function () {
            Route::controller(ProfitReportController::class)
                ->prefix('profit')
                ->name('profit.')
                ->group(function () {
                    Route::get('index', 'index')->name('index');
                });
            Route::controller(SalesReportController::class)
                ->prefix('sales')
                ->name('sales.')
                ->group(function () {
                    Route::get('index', 'index')->name('index');
                    Route::get('export', 'export')->name('export');
                });
        });
});

// Route::controller(PurchaseReturnController::class)
//     ->name('purchases_return.')
//     ->prefix('purchases_return')
//     ->group(function () {
//         Route::get('index', 'index')->name('index');
//         Route::post('store', 'store')->name('store');
//         Route::get('create', 'create')->name('create');
//         Route::get('edit/{purchase}', 'edit')->name('edit');
//     });
Route::controller(LoginController::class)
    ->group(function () {
        Route::middleware('guest')->group(function () {
            Route::get('login', 'login')->name('login');
            Route::post('login', 'authenticate')->name('login.auth');
        });
        Route::post('logout', 'logout')->name('logout');
    });
