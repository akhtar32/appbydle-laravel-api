<?php

use App\Http\Controllers\ControllerExpenses;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post("user/login", [UserController::class, "login"]);
Route::post("guest/login",[UserController::class,"guest_login"]);


Route::group(["middleware" => "auth:user-api"], function () {
    Route::post('expenses/create', [ControllerExpenses::class, "store"]);
    Route::get("expenses/get", [ControllerExpenses::class, "index"]);
    Route::get("expenses/delete/{id}", [ControllerExpenses::class, "delete"]);
});
