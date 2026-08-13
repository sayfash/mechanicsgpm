<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});

Route::get('/login', function () {
    return response()->json(['error' => 'Unauthenticated.'], 401);
})->name('login');

// Public printable invoice & PDF download routes (accessible via WhatsApp link)
Route::get('/print-customer-invoice', [\App\Http\Controllers\API\JobController::class, 'printCustomerInvoice']);
Route::get('/download-customer-invoice-pdf', [\App\Http\Controllers\API\JobController::class, 'downloadCustomerInvoicePdf']);

// Top-level printable maintenance record views (Protected by auth:web)
Route::middleware('auth:web')->group(function () {
    Route::get('/print-maintenance-record', [\App\Http\Controllers\API\JobController::class, 'printMaintenanceRecord']);
    Route::get('/maintenance/print/{job_id}', [\App\Http\Controllers\MaintenanceRecordController::class, 'printRecord'])
        ->name('maintenance.print');
});

Route::prefix('api')->group(function () {
    Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login'])->middleware('throttle:6,1');
    Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout']);
    Route::post('/forgot-password', [\App\Http\Controllers\AuthController::class, 'forgotPassword'])->middleware('throttle:3,1');
    Route::get('/me', [\App\Http\Controllers\AuthController::class, 'me']);

    Route::middleware('auth:web')->group(function () {
        Route::match(['get', 'post', 'put', 'delete'], '/legacy', [\App\Http\Controllers\LegacyApiController::class, 'handle']);
        Route::post('/update-profile', [\App\Http\Controllers\AuthController::class, 'updateProfile']);
        
        // Admin & Management Only Routes (Super Admin)
        Route::middleware('role:super_admin')->group(function () {
            Route::post('/branches', [\App\Http\Controllers\BranchController::class, 'store']);
            Route::put('/branches', [\App\Http\Controllers\BranchController::class, 'update']);
            Route::delete('/branches', [\App\Http\Controllers\BranchController::class, 'destroy']);

            Route::post('/users', [\App\Http\Controllers\API\UserController::class, 'store']);
            Route::put('/users', [\App\Http\Controllers\API\UserController::class, 'update']);
            Route::delete('/users', [\App\Http\Controllers\API\UserController::class, 'destroy']);

            Route::get('/audit-logs', [\App\Http\Controllers\API\AuditLogController::class, 'index']);
            Route::put('/audit-logs', [\App\Http\Controllers\API\AuditLogController::class, 'update']);

            Route::post('/common-issues', [\App\Http\Controllers\API\DashboardController::class, 'addCommonIssue']);
            Route::put('/common-issues', [\App\Http\Controllers\API\DashboardController::class, 'editCommonIssue']);
            Route::delete('/common-issues', [\App\Http\Controllers\API\DashboardController::class, 'deleteCommonIssue']);

            Route::post('/mechanic-form-items', [\App\Http\Controllers\API\DashboardController::class, 'addMechanicFormItem']);
            Route::put('/mechanic-form-items', [\App\Http\Controllers\API\DashboardController::class, 'editMechanicFormItem']);
            Route::delete('/mechanic-form-items', [\App\Http\Controllers\API\DashboardController::class, 'deleteMechanicFormItem']);

            Route::post('/other-services', [\App\Http\Controllers\API\DashboardController::class, 'addOtherService']);
            Route::put('/other-services', [\App\Http\Controllers\API\DashboardController::class, 'editOtherService']);
            Route::delete('/other-services', [\App\Http\Controllers\API\DashboardController::class, 'deleteOtherService']);

            Route::post('/service-options', [\App\Http\Controllers\API\DashboardController::class, 'addServiceOption']);
            Route::put('/service-options', [\App\Http\Controllers\API\DashboardController::class, 'editServiceOption']);
            Route::delete('/service-options', [\App\Http\Controllers\API\DashboardController::class, 'deleteServiceOption']);

            Route::post('/inventory/bulk-delete', [\App\Http\Controllers\API\InventoryController::class, 'bulkDestroy']);
        });

        // Reports Route (Super Admin & Manager)
        Route::middleware('role:super_admin,manager')->group(function () {
            Route::get('/reports/summary', [\App\Http\Controllers\API\ReportController::class, 'getSummary']);
        });

        // Common Data Retrieval Routes (accessible to all authenticated roles)
        Route::get('/branches', [\App\Http\Controllers\BranchController::class, 'index']);
        Route::get('/users', [\App\Http\Controllers\API\UserController::class, 'index']);
        Route::get('/dashboard-stats', [\App\Http\Controllers\API\DashboardController::class, 'getDashboardStats']);
        Route::get('/management-counts', [\App\Http\Controllers\API\DashboardController::class, 'getManagementCounts']);
        Route::get('/common-issues', [\App\Http\Controllers\API\DashboardController::class, 'getCommonIssues']);
        Route::get('/mechanic-form-items', [\App\Http\Controllers\API\DashboardController::class, 'getMechanicFormItems']);
        Route::get('/other-services', [\App\Http\Controllers\API\DashboardController::class, 'getOtherServices']);
        Route::get('/service-options', [\App\Http\Controllers\API\DashboardController::class, 'getServiceOptions']);

        // Mechanic Routes
        Route::post('/mechanic/check-in', [\App\Http\Controllers\MechanicController::class, 'mechanicCheckIn']);
        Route::get('/mechanic/jobs', [\App\Http\Controllers\MechanicController::class, 'getMechanicJobs']);
        Route::get('/mechanic/next-job-id', [\App\Http\Controllers\MechanicController::class, 'getNextJobId']);
        Route::get('/mechanic/lookup-customer', [\App\Http\Controllers\MechanicController::class, 'lookupCustomerByIdCard']);
        
        // Customer Routes (Read: all, Mutate: super_admin, shop_admin)
        Route::get('/customers', [\App\Http\Controllers\API\CustomerController::class, 'index']);
        Route::middleware('role:super_admin,shop_admin')->group(function () {
            Route::post('/customers', [\App\Http\Controllers\API\CustomerController::class, 'store']);
            Route::put('/customers', [\App\Http\Controllers\API\CustomerController::class, 'update']);
            Route::delete('/customers', [\App\Http\Controllers\API\CustomerController::class, 'destroy']);
            Route::post('/customers/batch', [\App\Http\Controllers\API\CustomerController::class, 'importCustomersBatch']);
            Route::post('/customers/register-with-vehicle', [\App\Http\Controllers\API\CustomerController::class, 'registerWithVehicle']);
        });

        // Vehicle Routes (Read: all, Mutate: super_admin, shop_admin)
        Route::get('/vehicles', [\App\Http\Controllers\API\VehicleController::class, 'index']);
        Route::middleware('role:super_admin,shop_admin')->group(function () {
            Route::post('/vehicles', [\App\Http\Controllers\API\VehicleController::class, 'store']);
            Route::put('/vehicles', [\App\Http\Controllers\API\VehicleController::class, 'update']);
            Route::delete('/vehicles', [\App\Http\Controllers\API\VehicleController::class, 'destroy']);
            Route::post('/vehicles/batch', [\App\Http\Controllers\API\VehicleController::class, 'importVehiclesBatch']);
            Route::post('/vehicles/bind-customer', [\App\Http\Controllers\API\VehicleController::class, 'bindCustomer']);
            Route::post('/vehicles/rebind', [\App\Http\Controllers\API\VehicleController::class, 'rebindVehicles']);
        });

        // Maintenance Records Routes
        Route::get('/maintenance-records', [\App\Http\Controllers\API\JobController::class, 'getCustomerMaintenanceRecords']);
        Route::get('/print-maintenance-record', [\App\Http\Controllers\API\JobController::class, 'printMaintenanceRecord']);
        Route::get('/print-customer-invoice', [\App\Http\Controllers\API\JobController::class, 'printCustomerInvoice']);
        Route::get('/download-customer-invoice-pdf', [\App\Http\Controllers\API\JobController::class, 'downloadCustomerInvoicePdf']);
        Route::middleware('role:super_admin,shop_admin,mechanic')->group(function () {
            Route::post('/jobs/start', [\App\Http\Controllers\API\JobController::class, 'startJob']);
            Route::post('/jobs/submit', [\App\Http\Controllers\API\JobController::class, 'submitMechanicJob']);
            Route::post('/jobs/cancel', [\App\Http\Controllers\API\JobController::class, 'cancelActiveJob']);
            Route::put('/maintenance-records', [\App\Http\Controllers\API\JobController::class, 'editMaintenanceRecord']);
            Route::delete('/maintenance-records', [\App\Http\Controllers\API\JobController::class, 'deleteMaintenanceRecord']);
            Route::post('/maintenance-records/batch', [\App\Http\Controllers\API\JobController::class, 'importRecordsBatch']);
        });
        Route::get('/api/print-maintenance-record', [\App\Http\Controllers\API\JobController::class, 'printMaintenanceRecord']);
        Route::get('/api/print-customer-invoice', [\App\Http\Controllers\API\JobController::class, 'printCustomerInvoice']);
        Route::get('/api/download-customer-invoice-pdf', [\App\Http\Controllers\API\JobController::class, 'downloadCustomerInvoicePdf']);
        Route::get('/maintenance-records/export', [\App\Http\Controllers\API\JobController::class, 'exportMaintenanceRecords']);

        // Read-only Inventory & Spare Parts (Super Admin, Manager, Shop Admin, Inventory Admin)
        Route::middleware('role:super_admin,manager,shop_admin,inventory_admin')->group(function () {
            Route::get('/inventory', [\App\Http\Controllers\API\InventoryController::class, 'index']);
            Route::get('/sparepart-categories', [\App\Http\Controllers\API\InventoryController::class, 'getCategories']);
            Route::get('/spare-parts-history', [\App\Http\Controllers\API\InventoryController::class, 'getHistory']);
        });

        // Mutating Inventory Routes (Super Admin, Shop Admin, Inventory Admin)
        Route::middleware('role:super_admin,shop_admin,inventory_admin')->group(function () {
            Route::post('/inventory', [\App\Http\Controllers\API\InventoryController::class, 'store']);
            Route::put('/inventory', [\App\Http\Controllers\API\InventoryController::class, 'update']);
            Route::delete('/inventory', [\App\Http\Controllers\API\InventoryController::class, 'destroy']);
            Route::post('/inventory/batch', [\App\Http\Controllers\API\InventoryController::class, 'importBatch']);
            Route::post('/sparepart-categories', [\App\Http\Controllers\API\InventoryController::class, 'addCategory']);
            Route::put('/sparepart-categories', [\App\Http\Controllers\API\InventoryController::class, 'editCategory']);
            Route::delete('/sparepart-categories', [\App\Http\Controllers\API\InventoryController::class, 'deleteCategory']);
        });
    });
});
