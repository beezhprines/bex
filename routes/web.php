<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\MarketerController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\OperatorController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::middleware(["auth"])->group(function () {

    // home
    Route::get("/", [HomeController::class, "dashboard"])->name("dashboard");
    Route::post("/calendar", [HomeController::class, "calendar"])->name("calendar");

    // masters
    Route::prefix("masters")->name("masters.")->group(function () {
        Route::get("statistics", [MasterController::class, "statistics"])->name("statistics");
    });

    // marketers
    Route::prefix('marketers')->name('marketers.')->group(function () {
        Route::get('analytics', [MarketerController::class, 'analytics'])->name('analytics');
        Route::post('saveteamoutcomes', [MarketerController::class, 'saveTeamOutcomes'])->name('saveTeamOutcomes');
        Route::get('diagrams', [MarketerController::class, 'diagrams'])->name('diagrams');
    });

    // operators
    Route::prefix('operators')->name('operators.')->group(function () {
        Route::get('statistics', [OperatorController::class, 'statistics'])->name('statistics');
        Route::get('salesplan', [OperatorController::class, 'salesplan'])->name('salesplan');
    });

    // invoices
    Route::resources(["invoices" => InvoiceController::class]);
    Route::prefix("invoices")->name("invoices.")->group(function () {
        Route::post("/store/many", [InvoiceController::class, "storeMany"])->name("store.many");
    });
});
