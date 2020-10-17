<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->items() as $item) {
            Role::create($item);
        }
    }

    private function items()
    {
        return [
            ['title' => 'host', 'code' => 'host'],
            ['title' => 'owner', 'code' => 'owner'],
            ['title' => 'Мастер', 'code' => 'master'],
            ['title' => 'Оператор', 'code' => 'operator'],
            ['title' => 'Макетолог', 'code' => 'marketer'],
            ['title' => 'Менеджер', 'code' => 'manager'],
        ];
    }
}
