<?php

namespace App\Services;

use App\Models\BudgetType;
use App\Models\City;
use App\Models\Configuration;
use App\Models\ContactType;
use App\Models\Country;
use App\Models\Currency;
use App\Models\CurrencyRate;
use App\Models\Manager;
use App\Models\Marketer;
use App\Models\Master;
use App\Models\Operator;
use App\Models\Role;
use App\Models\Service;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class RestoreService
{
    private string $filename = "dshpyrk3_bex_prd.json";
    private $collection;

    function __construct()
    {
        if (Storage::exists($this->filename)) {
            $this->collection = collect(json_decode(Storage::get($this->filename), true));
        }
    }

    public function restore(string $from, string $to)
    {
        $models = [
            ["tableName" => "currencies", "class" => Currency::class],
            ["tableName" => "currency_rates", "class" => CurrencyRate::class],
            ["tableName" => "countries", "class" => Country::class],
            ["tableName" => "cities", "class" => City::class],
            ["tableName" => "configurations", "class" => Configuration::class],
            ["tableName" => "roles", "class" => Role::class],
            ["tableName" => "users", "class" => User::class],
            ["tableName" => "teams", "class" => Team::class],
            ["tableName" => "budget_types", "class" => BudgetType::class],
            ["tableName" => "services", "class" => Service::class],
            ["tableName" => "contact_types", "class" => ContactType::class],
        ];

        foreach ($models as $model) {
            $this->restoreModel($model["tableName"], $model["class"]);
        }

        $employees = collect($this->collection->filter(function ($table) {
            return !empty($table["name"]) && $table["name"] == "employees";
        })->first()["data"]) ?? null;

        $this->restoreMasters($employees);
        $this->restoreManagers($employees);
        $this->restoreMarketers($employees);
        $this->restoreOperators($employees);
    }
    /*
    private function seedCurrencies(string $from, string $to)
    {
        $currencies = [
            ["title" => "Тенге", "code" => "KZT"],
            ["title" => "Рубль", "code" => "RUB"],
            ["title" => "Доллар", "code" => "USD"],
        ];

        $rates = [
            "KZT" => 1,
            "RUB" => 5.6,
            "USD" => 488
        ];

        foreach ($currencies as $currency) {
            $currency = Currency::create($currency);
            foreach (daterange($from, $to, true) as $date) {
                CurrencyRate::create([
                    "date" => date_format($date, config("app.iso_date")),
                    "rate" => $rates[$currency->code],
                    "currency_id" => $currency->id
                ]);
            }
        }
    }
*/
    private function restoreMasters($employees)
    {
        $items = collect($this->collection->filter(function ($table) {
            return !empty($table["name"]) && $table["name"] == "masters";
        })->first()["data"]) ?? null;

        Master::truncate();

        foreach ($items as $item) {
            $employee = $employees->where("id", $item["employee_id"])->first();
            $item["user_id"] = $employee["user_id"] ?? null;
            $item["name"] = $employee["name"] ?? null;
            Master::create($item);
        }
    }

    private function restoreOperators($employees)
    {
        $items = collect($this->collection->filter(function ($table) {
            return !empty($table["name"]) && $table["name"] == "operators";
        })->first()["data"]) ?? null;

        Operator::truncate();

        foreach ($items as $item) {
            $employee = $employees->where("id", $item["employee_id"])->first();
            $item["user_id"] = $employee["user_id"] ?? null;
            $item["name"] = $employee["name"] ?? null;
            Operator::create($item);
        }
    }

    private function restoreMarketers($employees)
    {
        $items = collect($this->collection->filter(function ($table) {
            return !empty($table["name"]) && $table["name"] == "marketers";
        })->first()["data"]) ?? null;

        Marketer::truncate();

        foreach ($items as $item) {
            $employee = $employees->where("id", $item["employee_id"])->first();
            $item["user_id"] = $employee["user_id"] ?? null;
            $item["name"] = $employee["name"] ?? null;
            Marketer::create($item);
        }
    }

    private function restoreManagers($employees)
    {
        $items = collect($this->collection->filter(function ($table) {
            return !empty($table["name"]) && $table["name"] == "managers";
        })->first()["data"]) ?? null;

        Manager::truncate();

        foreach ($items as $item) {
            $employee = $employees->where("id", $item["employee_id"])->first();
            $item["user_id"] = $employee["user_id"] ?? null;
            $item["name"] = $employee["name"] ?? null;
            Manager::create($item);
        }
    }

    private function restoreModel(string $tableName, string $model)
    {
        $items = collect($this->collection->filter(function ($table) use ($tableName) {
            return !empty($table["name"]) && $table["name"] == $tableName;
        })->first()["data"]) ?? null;

        $model::truncate();

        foreach ($items as $item) {
            $model::create($item);
        }
    }
}
