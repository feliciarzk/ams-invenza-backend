<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\MenuPriceController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PurchaseItemController;
use App\Http\Controllers\ReceivingController;
use App\Http\Controllers\ReceivingItemController;
use App\Http\Controllers\ReimbursementController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\StockOpnameController;
use App\Http\Controllers\StockOpnameItemController;
use App\Http\Controllers\StockPolicyController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\TransferItemController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/test', function () {
    return response()->json([
        'message' => 'Laravel API is working',
    ]);
});

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->get('/me', function () {
    $user = auth()->user()->load(['role', 'location']);

    return response()->json([
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role_id' => $user->role_id,
            'role' => $user->role?->name,
            'location_id' => $user->location_id,
            'location' => $user->location?->name,
            'position' => $user->position,
            'is_active' => $user->is_active,
        ],
    ]);
});

Route::middleware('auth:api')->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/roles', [RoleController::class, 'index']);
    Route::get('/locations', [LocationController::class, 'index']);
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/items', [ItemController::class, 'index']);
    Route::get('/suppliers', [SupplierController::class, 'index']);
    Route::get('/stocks', [StockController::class, 'index']);
    Route::get('/stock-movements', [StockMovementController::class, 'index']);
    Route::get('/stock-policies', [StockPolicyController::class, 'index']);
    Route::get('/purchases', [PurchaseController::class, 'index']);
    Route::get('/purchase-items', [PurchaseItemController::class, 'index']);
    Route::get('/receivings', [ReceivingController::class, 'index']);
    Route::get('/receiving-items', [ReceivingItemController::class, 'index']);
    Route::get('/transfers', [TransferController::class, 'index']);
    Route::get('/transfer-items', [TransferItemController::class, 'index']);
    Route::get('/stock-opnames', [StockOpnameController::class, 'index']);
    Route::get('/stock-opname-items', [StockOpnameItemController::class, 'index']);
    Route::get('/expenses', [ExpenseController::class, 'index']);
    Route::get('/reimbursements', [ReimbursementController::class, 'index']);
    Route::get('/menu-prices', [MenuPriceController::class, 'index']);
});

Route::middleware(['auth:api', 'role:Manager'])->group(function () {
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);

    Route::post('/roles', [RoleController::class, 'store']);
    Route::put('/roles/{id}', [RoleController::class, 'update']);
    Route::delete('/roles/{id}', [RoleController::class, 'destroy']);

    Route::post('/locations', [LocationController::class, 'store']);
    Route::put('/locations/{id}', [LocationController::class, 'update']);
    Route::delete('/locations/{id}', [LocationController::class, 'destroy']);

    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

    Route::post('/items', [ItemController::class, 'store']);
    Route::put('/items/{id}', [ItemController::class, 'update']);
    Route::delete('/items/{id}', [ItemController::class, 'destroy']);

    Route::post('/suppliers', [SupplierController::class, 'store']);
    Route::put('/suppliers/{id}', [SupplierController::class, 'update']);
    Route::delete('/suppliers/{id}', [SupplierController::class, 'destroy']);

    Route::post('/stocks', [StockController::class, 'store']);
    Route::put('/stocks/{id}', [StockController::class, 'update']);
    Route::delete('/stocks/{id}', [StockController::class, 'destroy']);

    Route::post('/stock-movements', [StockMovementController::class, 'store']);
    Route::put('/stock-movements/{id}', [StockMovementController::class, 'update']);
    Route::delete('/stock-movements/{id}', [StockMovementController::class, 'destroy']);

    Route::post('/stock-policies', [StockPolicyController::class, 'store']);
    Route::put('/stock-policies/{id}', [StockPolicyController::class, 'update']);
    Route::delete('/stock-policies/{id}', [StockPolicyController::class, 'destroy']);

    Route::post('/purchases', [PurchaseController::class, 'store']);
    Route::put('/purchases/{id}', [PurchaseController::class, 'update']);
    Route::delete('/purchases/{id}', [PurchaseController::class, 'destroy']);

    Route::post('/purchase-items', [PurchaseItemController::class, 'store']);
    Route::put('/purchase-items/{id}', [PurchaseItemController::class, 'update']);
    Route::delete('/purchase-items/{id}', [PurchaseItemController::class, 'destroy']);

    Route::post('/receivings', [ReceivingController::class, 'store']);
    Route::put('/receivings/{id}', [ReceivingController::class, 'update']);
    Route::delete('/receivings/{id}', [ReceivingController::class, 'destroy']);

    Route::post('/receiving-items', [ReceivingItemController::class, 'store']);
    Route::put('/receiving-items/{id}', [ReceivingItemController::class, 'update']);
    Route::delete('/receiving-items/{id}', [ReceivingItemController::class, 'destroy']);

    Route::post('/transfers', [TransferController::class, 'store']);
    Route::put('/transfers/{id}', [TransferController::class, 'update']);
    Route::delete('/transfers/{id}', [TransferController::class, 'destroy']);

    Route::post('/transfer-items', [TransferItemController::class, 'store']);
    Route::put('/transfer-items/{id}', [TransferItemController::class, 'update']);
    Route::delete('/transfer-items/{id}', [TransferItemController::class, 'destroy']);

    Route::post('/stock-opnames', [StockOpnameController::class, 'store']);
    Route::put('/stock-opnames/{id}', [StockOpnameController::class, 'update']);
    Route::delete('/stock-opnames/{id}', [StockOpnameController::class, 'destroy']);

    Route::post('/stock-opname-items', [StockOpnameItemController::class, 'store']);
    Route::put('/stock-opname-items/{id}', [StockOpnameItemController::class, 'update']);
    Route::delete('/stock-opname-items/{id}', [StockOpnameItemController::class, 'destroy']);

    Route::post('/expenses', [ExpenseController::class, 'store']);
    Route::put('/expenses/{id}', [ExpenseController::class, 'update']);
    Route::delete('/expenses/{id}', [ExpenseController::class, 'destroy']);

    Route::post('/reimbursements', [ReimbursementController::class, 'store']);
    Route::put('/reimbursements/{id}', [ReimbursementController::class, 'update']);
    Route::delete('/reimbursements/{id}', [ReimbursementController::class, 'destroy']);

    Route::post('/menu-prices', [MenuPriceController::class, 'store']);
    Route::put('/menu-prices/{id}', [MenuPriceController::class, 'update']);
    Route::delete('/menu-prices/{id}', [MenuPriceController::class, 'destroy']);
});