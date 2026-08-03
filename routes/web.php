<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});

Route::get('/login', function () {
    return response()->json(['error' => 'Unauthenticated.'], 401);
})->name('login');

Route::prefix('api')->group(function () {
    Route::match(['get', 'post', 'put', 'delete'], '/legacy', [\App\Http\Controllers\LegacyApiController::class, 'handle']);
    Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login']);
    Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout']);
    Route::post('/forgot-password', [\App\Http\Controllers\AuthController::class, 'forgotPassword']);

    Route::middleware('auth:web')->group(function () {
        Route::get('/me', [\App\Http\Controllers\AuthController::class, 'me']);
        Route::post('/update-profile', [\App\Http\Controllers\AuthController::class, 'updateProfile']);
        
        // Branch Routes
        Route::get('/branches', [\App\Http\Controllers\BranchController::class, 'index']);
        Route::post('/branches', [\App\Http\Controllers\BranchController::class, 'store']);
        Route::put('/branches', [\App\Http\Controllers\BranchController::class, 'update']);
        Route::delete('/branches', [\App\Http\Controllers\BranchController::class, 'destroy']);

        // Dashboard & Analytics
        Route::get('/dashboard-stats', [\App\Http\Controllers\API\DashboardController::class, 'getDashboardStats']);
        Route::get('/management-counts', [\App\Http\Controllers\API\DashboardController::class, 'getManagementCounts']);
        Route::get('/common-issues', [\App\Http\Controllers\API\DashboardController::class, 'getCommonIssues']);
        Route::post('/common-issues', [\App\Http\Controllers\API\DashboardController::class, 'addCommonIssue']);
        Route::put('/common-issues', [\App\Http\Controllers\API\DashboardController::class, 'editCommonIssue']);
        Route::delete('/common-issues', [\App\Http\Controllers\API\DashboardController::class, 'deleteCommonIssue']);
        Route::get('/mechanic-form-items', [\App\Http\Controllers\API\DashboardController::class, 'getMechanicFormItems']);
        Route::post('/mechanic-form-items', [\App\Http\Controllers\API\DashboardController::class, 'addMechanicFormItem']);
        Route::put('/mechanic-form-items', [\App\Http\Controllers\API\DashboardController::class, 'editMechanicFormItem']);
        Route::delete('/mechanic-form-items', [\App\Http\Controllers\API\DashboardController::class, 'deleteMechanicFormItem']);
        Route::get('/other-services', [\App\Http\Controllers\API\DashboardController::class, 'getOtherServices']);
        Route::post('/other-services', [\App\Http\Controllers\API\DashboardController::class, 'addOtherService']);
        Route::put('/other-services', [\App\Http\Controllers\API\DashboardController::class, 'editOtherService']);
        Route::delete('/other-services', [\App\Http\Controllers\API\DashboardController::class, 'deleteOtherService']);
        Route::get('/service-options', [\App\Http\Controllers\API\DashboardController::class, 'getServiceOptions']);
        Route::post('/service-options', [\App\Http\Controllers\API\DashboardController::class, 'addServiceOption']);
        Route::put('/service-options', [\App\Http\Controllers\API\DashboardController::class, 'editServiceOption']);
        Route::delete('/service-options', [\App\Http\Controllers\API\DashboardController::class, 'deleteServiceOption']);
        Route::get('/audit-logs', [\App\Http\Controllers\API\AuditLogController::class, 'index']);
        Route::put('/audit-logs', [\App\Http\Controllers\API\AuditLogController::class, 'update']);

        // User Management
        Route::get('/users', [\App\Http\Controllers\API\UserController::class, 'index']);
        Route::post('/users', [\App\Http\Controllers\API\UserController::class, 'store']);
        Route::put('/users', [\App\Http\Controllers\API\UserController::class, 'update']);
        Route::delete('/users', [\App\Http\Controllers\API\UserController::class, 'destroy']);

        // Mechanic Routes
        Route::post('/mechanic/check-in', [\App\Http\Controllers\MechanicController::class, 'mechanicCheckIn']);
        Route::get('/mechanic/jobs', [\App\Http\Controllers\MechanicController::class, 'getMechanicJobs']);
        Route::get('/mechanic/next-job-id', [\App\Http\Controllers\MechanicController::class, 'getNextJobId']);
        Route::get('/mechanic/lookup-customer', [\App\Http\Controllers\MechanicController::class, 'lookupCustomerByIdCard']);
        
        // Customer Routes
        Route::get('/customers', [\App\Http\Controllers\API\CustomerController::class, 'index']);
        Route::post('/customers', [\App\Http\Controllers\API\CustomerController::class, 'store']);
        Route::put('/customers', [\App\Http\Controllers\API\CustomerController::class, 'update']);
        Route::delete('/customers', [\App\Http\Controllers\API\CustomerController::class, 'destroy']);
        Route::post('/customers/batch', [\App\Http\Controllers\API\CustomerController::class, 'importCustomersBatch']);
        Route::post('/customers/register-with-vehicle', [\App\Http\Controllers\API\CustomerController::class, 'registerWithVehicle']);

        // Vehicle Routes
        Route::get('/vehicles', [\App\Http\Controllers\API\VehicleController::class, 'index']);
        Route::post('/vehicles', [\App\Http\Controllers\API\VehicleController::class, 'store']);
        Route::put('/vehicles', [\App\Http\Controllers\API\VehicleController::class, 'update']);
        Route::delete('/vehicles', [\App\Http\Controllers\API\VehicleController::class, 'destroy']);
        Route::post('/vehicles/batch', [\App\Http\Controllers\API\VehicleController::class, 'importVehiclesBatch']);
        Route::post('/vehicles/bind-customer', [\App\Http\Controllers\API\VehicleController::class, 'bindCustomer']);
        
        // Job / Maintenance Records
        Route::post('/jobs/submit', [\App\Http\Controllers\API\JobController::class, 'submitMechanicJob']);
        Route::get('/maintenance-records', [\App\Http\Controllers\API\JobController::class, 'getCustomerMaintenanceRecords']);
        Route::put('/maintenance-records', [\App\Http\Controllers\API\JobController::class, 'editMaintenanceRecord']);
        Route::delete('/maintenance-records', [\App\Http\Controllers\API\JobController::class, 'deleteMaintenanceRecord']);
        Route::post('/maintenance-records/batch', [\App\Http\Controllers\API\JobController::class, 'importRecordsBatch']);

        // Inventory Routes
        Route::get('/inventory', [\App\Http\Controllers\API\InventoryController::class, 'index']);
        Route::post('/inventory', [\App\Http\Controllers\API\InventoryController::class, 'store']);
        Route::put('/inventory', [\App\Http\Controllers\API\InventoryController::class, 'update']);
        Route::delete('/inventory', [\App\Http\Controllers\API\InventoryController::class, 'destroy']);
        Route::post('/inventory/bulk-delete', [\App\Http\Controllers\API\InventoryController::class, 'bulkDestroy']);
        Route::post('/inventory/batch', [\App\Http\Controllers\API\InventoryController::class, 'importBatch']);
        Route::get('/sparepart-categories', [\App\Http\Controllers\API\InventoryController::class, 'getCategories']);
        Route::post('/sparepart-categories', [\App\Http\Controllers\API\InventoryController::class, 'addCategory']);
        Route::put('/sparepart-categories', [\App\Http\Controllers\API\InventoryController::class, 'editCategory']);
        Route::delete('/sparepart-categories', [\App\Http\Controllers\API\InventoryController::class, 'deleteCategory']);
        Route::get('/spare-parts-history', [\App\Http\Controllers\API\InventoryController::class, 'getHistory']);
    });
});
