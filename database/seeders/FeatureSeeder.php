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
        echo "\n";
        echo "seed 1";
        echo "\n";

        $newBudgetType =
            [
                "title" => "Непредвиденный расход маркетолога",
                "code" => "marketer:unexpected:outcome",
                "income" => 0
            ];
        $budgetType = BudgetType::firstWhere("code", "marketer:unexpected:outcome");
        if (empty($budgetType)) {
            echo "\n";
            echo "seed 2";
            echo "\n";
            BudgetType::create($newBudgetType);
        }
    }
}
