<?php

namespace Database\Seeders;

use App\Models\Manager;
use App\Models\Role;
use Illuminate\Database\Seeder;

class FeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            [
                "title" => "HR менеджер",
                "code" => "recruiter",
            ],
            [
                "title" => "Главный оператор",
                "code" => "chief-operator",
            ],
        ];

        foreach ($roles as $role) {
            if (empty(Role::firstWhere("code", $role["code"]))) {
                Role::create($role);
            }
        }

        $roles = [
            "Beauty Expert - Master" => "Мастер",
            "Beauty Expert - Cosmetologist" => "Косметолог",
            "Beauty Expert - Operator" => "Оператор",
            "Beauty Expert - Marketer" => "Маркетолог",
            "Beauty Expert - Manager" => "Менеджер",
        ];

        foreach ($roles as $en => $ru) {
            $role = Role::firstWhere("title", $en);
            $role->update([
                "title" => $ru
            ]);
        }

        $managersToMigrate = [
            ["name" => "Айнура Искакова", "role" => "recruiter"],
            ["name" => "Елена", "role" => "chief-operator"],
        ];

        foreach ($managersToMigrate as $item) {
            $manager = Manager::firstWhere("name", $item["name"]);
            $user = $manager->user;
            $roleToMigrate = Role::firstWhere("code", $item["role"]);
            $user->update([
                "role_id" => $roleToMigrate->id
            ]);
        }
    }
}
