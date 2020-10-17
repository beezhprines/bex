<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->items() as $item) {
            $item['email_verified_at'] = now();
            $item['password'] = bcrypt('1234qwer');
            $item['remember_token'] = Str::random(10);
            User::create($item);
        }
    }

    private function items()
    {
        return [
            [
                'account' => 'sayat.a',
                'email' => 'amanbayev.sayat@gmail.com',
                'phone' => '+77763442424',
                'role_id' => Role::findByCode("host")->id
            ]
        ];
    }
}
