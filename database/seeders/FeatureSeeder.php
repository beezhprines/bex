<?php

namespace Database\Seeders;

use App\Models\BudgetType;
use App\Models\Contact;
use App\Models\Manager;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class FeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Contact::truncate();

        Artisan::call("migrate");

        $newBudgetType =
            [
                "title" => "Непредвиденный доход мастера",
                "code" => "master:unexpected:income",
                "income" => 1
            ];

        $budgetType = BudgetType::firstWhere("code", "master:unexpected:income");
        if (empty($budgetType)) {
            BudgetType::create($newBudgetType);
        }
    }
}
