<?php

namespace Database\Seeders;

use App\Models\Budget;
use App\Models\BudgetType;
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

        $from = "2021-02-01";
        $to = "2021-03-19";


foreach (daterange($from, $to, true) as $date) {
    $date = date_format($date, config("app.iso_date"));
    $dates[] = $date;
}
        $dateSource = date(config("app.iso_date"), strtotime("2021-03-17"));
        $budgetTypeSource = BudgetType::findByCode("custom:month:outcome");
        $budgetSource = Budget::findByDateAndType($dateSource, $budgetTypeSource);

        foreach ($dates as $date) {
            $budgetType = BudgetType::findByCode("custom:month:outcome");
            $budget = Budget::findByDateAndType($date, $budgetType);
            if (!empty($budget)) {
            $budget->update([
                'json'=>$budgetSource->json
            ]);
                Budget::solveCustomOutcomes($date);
            }
        }

    }
}
