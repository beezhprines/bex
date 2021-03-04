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

        $role = Role::findByCode("chief-operator");
        if (!empty($role)) {

            $role->update([
                "title" => "Оператор вых. дня"
            ]);
        }
    }
}
