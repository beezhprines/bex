<?php

use App\Http\Controllers\ChartController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\ConfigurationController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CosmetologistController;
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
use App\Http\Controllers\UserController;
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

Route::get("/pull", [HomeController::class, "pull"])->name("pull");
Route::get("/db/backup", [HomeController::class, "dbbackup"])->name("db.backup");

Route::middleware(["auth"])->group(function () {

    // home
    Route::get("/", [HomeController::class, "dashboard"])->name("dashboard");
    Route::get("/calendar", [HomeController::class, "calendar"])->name("calendar");
    Route::post("/denied", [HomeController::class, "denied"])->name("denied");
    Route::post("/cache/clear", [HomeController::class, "cacheClear"])->name("cache.clear");

    // logs
    Route::get("logs", "\Rap2hpoutre\LaravelLogViewer\LogViewerController@index")->middleware("can:can-host");

    // masters
    Route::prefix("masters")->name("masters.")->group(function () {
        Route::get("/statistics", [MasterController::class, "statistics"])->name("statistics");
        Route::get("/services", [MasterController::class, "services"])->name("services");
        Route::post("/{master}/services/update", [MasterController::class, "servicesUpdate"])->name("services.update");
        Route::get("/", [MasterController::class, "index"])->name("index");
        Route::post("/update/{master}", [MasterController::class, "update"])->name("update");
        Route::put("/load/all", [MasterController::class, "loadAll"])->name("load.all");
        Route::put("/load/{master}", [MasterController::class, "load"])->name("load");
        Route::put("/auth/{master}", [MasterController::class, "auth"])->name("auth");
        Route::put("/update/comissions", [MasterController::class, "updateUnexpectedComissions"])->name("update.comissions");
    });

    // cosmetologists
    Route::prefix("cosmetologists")->name("cosmetologists.")->group(function () {
        Route::get("/", [CosmetologistController::class, "index"])->name("index");
        Route::post("/update/{cosmetologist}", [CosmetologistController::class, "update"])->name("update");
        Route::put("/update/comissions", [CosmetologistController::class, "updateComissions"])->name("update.comissions");
        Route::put("/load/all", [CosmetologistController::class, "loadAll"])->name("load.all");
        Route::put("/load/{cosmetologist}", [CosmetologistController::class, "load"])->name("load");
        Route::put("/auth/{cosmetologist}", [CosmetologistController::class, "auth"])->name("auth");
    });


    // marketers
    Route::prefix("marketers")->name("marketers.")->group(function () {
        Route::get("/analytics", [MarketerController::class, "analytics"])->name("analytics");
        Route::post("/save/teamoutcomes", [MarketerController::class, "saveTeamOutcomes"])->name("saveTeamOutcomes");
        Route::get("/diagrams", [MarketerController::class, "diagrams"])->name("diagrams");
        Route::get("/", [MarketerController::class, "index"])->name("index");
        Route::post("/store", [MarketerController::class, "store"])->name("store");
        Route::put("/update/{marketer}", [MarketerController::class, "update"])->name("update");
        Route::delete("/{marketer}", [MarketerController::class, "destroy"])->name("destroy");
        Route::put("/auth/{marketer}", [MarketerController::class, "auth"])->name("auth");
    });

    // operators
    Route::prefix("operators")->name("operators.")->group(function () {
        Route::get("/statistics", [OperatorController::class, "statistics"])->name("statistics");
        Route::get("/salesplan", [OperatorController::class, "salesplan"])->name("salesplan");
        Route::get("/", [OperatorController::class, "index"])->name("index");
        Route::post("/store", [OperatorController::class, "store"])->name("store");
        Route::put("/update/{operator}", [OperatorController::class, "update"])->name("update");
        Route::delete("/{operator}", [OperatorController::class, "destroy"])->name("destroy");
        Route::put("/auth/{operator}", [OperatorController::class, "auth"])->name("auth");
    });

    // managers
    Route::prefix("managers")->name("managers.")->group(function () {
        Route::get("/weekplan", [ManagerController::class, "weekplan"])->name("weekplan");
        Route::get("/statistics", [ManagerController::class, "statistics"])->name("statistics");
        Route::get("/contacts", [ManagerController::class, "contacts"])->name("contacts");
        Route::get("/monitoring", [ManagerController::class, "monitoring"])->name("monitoring");
        Route::get("/comissions", [ManagerController::class, "comissions"])->name("comissions");
        Route::get("/cosmetologists", [ManagerController::class, "cosmetologists"])->name("cosmetologists");
        Route::get("/masters", [ManagerController::class, "masters"])->name("masters");
        Route::get("/currencyRates", [ManagerController::class, "currencyRates"])->name("currencyRates");
        Route::get("/", [ManagerController::class, "index"])->name("index");
        Route::post("/store", [ManagerController::class, "store"])->name("store");
        Route::put("/update/{manager}", [ManagerController::class, "update"])->name("update");
        Route::delete("/{manager}", [ManagerController::class, "destroy"])->name("destroy");
        Route::put("/auth/{manager}", [ManagerController::class, "auth"])->name("auth");
        Route::post("/sync", [ManagerController::class, "sync"])->name("sync");
    });

    // contacts
    Route::prefix("contacts")->name("contacts.")->group(function () {
        Route::post("/saveMany", [ContactController::class, "saveMany"])->name("saveMany");
    });

    // services
    Route::prefix("services")->name("services.")->group(function () {
        Route::post("/store/{master}", [ServiceController::class, "store"])->name("store");
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
        Route::put("/update/{configuration}", [ConfigurationController::class, "update"])->name("update");
    });

    // invoices
    Route::prefix("invoices")->name("invoices.")->group(function () {
        Route::delete("/{invoice}", [InvoiceController::class, "destroy"])->name("destroy");
        Route::post("/store/many", [InvoiceController::class, "storeMany"])->name("store.many");
        Route::patch("/confirm", [InvoiceController::class, "confirm"])->name("confirm");
    });

    // finances
    Route::prefix("finances")->name("finances.")->group(function () {
        Route::get("/statistics", [FinanceController::class, "statistics"])->name("statistics");
        Route::get("/customOutcomes", [FinanceController::class, "customOutcomes"])->name("customOutcomes");
        Route::post("/customOutcomes/update", [FinanceController::class, "updateCustomOutcomes"])->name("customOutcomes.update");
        Route::get("/payments", [FinanceController::class, "payments"])->name("payments");
        Route::put("/pay/budgets/manager/{manager}", [FinanceController::class, "payManagerBudgets"])->name("pay.manager.budgets");
        Route::put("/pay/budgets/operator/{operator}", [FinanceController::class, "payOperatorBudgets"])->name("pay.operator.budgets");
    });

    // users
    Route::prefix("users")->name("users.")->group(function () {
        Route::get("/profile", [UserController::class, "profile"])->name("profile");
        Route::patch("/update/{user}", [UserController::class, "update"])->name("update");
    });

    // charts
    Route::prefix("charts")->name("charts.")->group(function () {
        Route::get("/chats", [ChartController::class, "chats"])->name("chats");
        Route::get("/conversion", [ChartController::class, "conversion"])->name("conversion");
    });
});
