<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ItemController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// register admin user, for adding or updating items

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->get('/items', [ItemController::class, 'index']);
Route::middleware('auth:sanctum')->post('/items/create', [ItemController::class, 'store']);
Route::middleware('auth:sanctum')->get('/items/{id}', [ItemController::class, 'show']);
Route::middleware('auth:sanctum')->put('/items/update/{id}', [ItemController::class, 'update']);
Route::middleware('auth:sanctum')->delete('/items/delete/{id}', [ItemController::class, 'destroy']);

Route::middleware('auth:sanctum')->get('/inventories', [InventoryController::class, 'index']);
Route::middleware('auth:sanctum')->get('/inventories/{barcode}', [InventoryController::class, 'initInventory']);
Route::middleware('auth:sanctum')->post('/inventories/create', [InventoryController::class, 'store']);
Route::middleware('auth:sanctum')->get('/inventories/{id}', [InventoryController::class, 'show']);
Route::middleware('auth:sanctum')->put('/items/update/{id}', [ItemController::class, 'update']);
Route::middleware('auth:sanctum')->delete('/items/delete/{id}', [ItemController::class, 'destroy']);

Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

    



