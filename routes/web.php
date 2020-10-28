<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\MarketerController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\OperatorController;
use App\Http\Controllers\ServiceController;
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
        Route::get('/', [MasterController::class, 'index'])->name('index');
        Route::post('/update/{master}', [MasterController::class, 'update'])->name('update');
        Route::put('/load/all', [MasterController::class, 'loadAll'])->name('load.all');
        Route::put('/load/{master}', [MasterController::class, 'load'])->name('load');
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

    // managers
    Route::prefix('managers')->name('managers.')->group(function () {
        Route::get("weekplan", [ManagerController::class, "weekplan"])->name("weekplan");
        Route::get("statistics", [ManagerController::class, "statistics"])->name("statistics");
        Route::get("diagrams", [ManagerController::class, "diagrams"])->name("diagrams");
        Route::get("monitoring", [ManagerController::class, "monitoring"])->name("monitoring");
        Route::get("comissions", [ManagerController::class, "comissions"])->name("comissions");
        Route::get("currencyRates", [ManagerController::class, "currencyRates"])->name("currencyRates");
    });

    // contacts
    Route::prefix("contacts")->name("contacts.")->group(function () {
        Route::post("saveMany", [ContactController::class, "saveMany"])->name("saveMany");
    });

    // services
    Route::prefix("services")->name("services.")->group(function () {
        Route::post("store/{master}", [ServiceController::class, "store"])->name("store");
    });

    // invoices
    Route::resources(["invoices" => InvoiceController::class]);
    Route::prefix("invoices")->name("invoices.")->group(function () {
        Route::post("/store/many", [InvoiceController::class, "storeMany"])->name("store.many");
    });
});
