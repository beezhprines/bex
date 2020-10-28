<?php

use App\Http\Controllers\CityController;
use App\Http\Controllers\ConfigurationController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\MarketerController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\OperatorController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\TeamController;
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
        Route::put('/auth/{master}', [MasterController::class, 'auth'])->name('auth');
    });

    // marketers
    Route::prefix('marketers')->name('marketers.')->group(function () {
        Route::get('analytics', [MarketerController::class, 'analytics'])->name('analytics');
        Route::post('saveteamoutcomes', [MarketerController::class, 'saveTeamOutcomes'])->name('saveTeamOutcomes');
        Route::get('diagrams', [MarketerController::class, 'diagrams'])->name('diagrams');
        Route::get('/', [MarketerController::class, 'index'])->name('index');
        Route::post('/store', [MarketerController::class, 'store'])->name('store');
        Route::put('/update/{marketer}', [MarketerController::class, 'update'])->name('update');
        Route::delete('/{marketer}', [MarketerController::class, 'destroy'])->name('destroy');
        Route::put('/auth/{marketer}', [MarketerController::class, 'auth'])->name('auth');
    });

    // operators
    Route::prefix('operators')->name('operators.')->group(function () {
        Route::get('statistics', [OperatorController::class, 'statistics'])->name('statistics');
        Route::get('salesplan', [OperatorController::class, 'salesplan'])->name('salesplan');
        Route::get('/', [OperatorController::class, 'index'])->name('index');
        Route::post('/store', [OperatorController::class, 'store'])->name('store');
        Route::put('/update/{operator}', [OperatorController::class, 'update'])->name('update');
        Route::delete('/{operator}', [OperatorController::class, 'destroy'])->name('destroy');
        Route::put('/auth/{operator}', [OperatorController::class, 'auth'])->name('auth');
    });

    // managers
    Route::prefix('managers')->name('managers.')->group(function () {
        Route::get("weekplan", [ManagerController::class, "weekplan"])->name("weekplan");
        Route::get("statistics", [ManagerController::class, "statistics"])->name("statistics");
        Route::get("diagrams", [ManagerController::class, "diagrams"])->name("diagrams");
        Route::get("monitoring", [ManagerController::class, "monitoring"])->name("monitoring");
        Route::get("comissions", [ManagerController::class, "comissions"])->name("comissions");
        Route::get("currencyRates", [ManagerController::class, "currencyRates"])->name("currencyRates");
        Route::get('/', [ManagerController::class, 'index'])->name('index');
        Route::post('/store', [ManagerController::class, 'store'])->name('store');
        Route::put('/update/{manager}', [ManagerController::class, 'update'])->name('update');
        Route::delete('/{manager}', [ManagerController::class, 'destroy'])->name('destroy');
        Route::put('/auth/{manager}', [ManagerController::class, 'auth'])->name('auth');
    });

    // contacts
    Route::prefix("contacts")->name("contacts.")->group(function () {
        Route::post("saveMany", [ContactController::class, "saveMany"])->name("saveMany");
    });

    // services
    Route::prefix("services")->name("services.")->group(function () {
        Route::post("store/{master}", [ServiceController::class, "store"])->name("store");
    });

    // teams
    Route::prefix("teams")->name("teams.")->group(function () {
        Route::get("/", [TeamController::class, "index"])->name("index");
        Route::post("/update/all", [TeamController::class, "updateAll"])->name("update.all");
        Route::post("/store", [TeamController::class, "store"])->name("store");
    });

    // cities
    Route::prefix("cities")->name("cities.")->group(function () {
        Route::get("/", [CityController::class, "index"])->name("index");
        Route::post("/update/all", [CityController::class, "updateAll"])->name("update.all");
        Route::post("/store", [CityController::class, "store"])->name("store");
    });

    // countries
    Route::prefix("countries")->name("countries.")->group(function () {
        Route::get("/", [CountryController::class, "index"])->name("index");
        Route::post("/update/all", [CountryController::class, "updateAll"])->name("update.all");
        Route::post("/store", [CountryController::class, "store"])->name("store");
    });

    // currencies
    Route::prefix("currencies")->name("currencies.")->group(function () {
        Route::get("/", [CurrencyController::class, "index"])->name("index");
        Route::post("/update/all", [CurrencyController::class, "updateAll"])->name("update.all");
        Route::post("/store", [CurrencyController::class, "store"])->name("store");
    });

    // configurations
    Route::prefix("configurations")->name("configurations.")->group(function () {
        Route::get("/bonuses", [ConfigurationController::class, "bonuses"])->name("bonuses");
        Route::put("/udpate/{configuration}", [ConfigurationController::class, "update"])->name("update");
    });

    // invoices
    Route::prefix("invoices")->name("invoices.")->group(function () {
        Route::delete('/{invoice}', [InvoiceController::class, 'destroy'])->name('destroy');
        Route::post("/store/many", [InvoiceController::class, "storeMany"])->name("store.many");
    });

    // finances
    Route::prefix("finances")->name("finances.")->group(function () {
        Route::get('/statistics', [FinanceController::class, 'statistics'])->name('statistics');
        Route::get('/customOutcomes', [FinanceController::class, 'customOutcomes'])->name('customOutcomes');
        Route::post('/customOutcomes/update', [FinanceController::class, 'updateCustomOutcomes'])->name('customOutcomes.update');
    });
});
