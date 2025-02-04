<?php

namespace Database\Seeders;

use App\Models\User;
use App\UserRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'username' => 'admin',
                'name' => 'Admin',
                'role' => UserRole::ADMIN->value,
                'password' => bcrypt('admin')
            ],
            [
                'username' => 'kasir',
                'name' => 'Kasir',
                'role' => UserRole::KASIR->value,
                'password' => bcrypt('kasir')
            ],
        ];

        foreach ($data as $key => $value) {
            User::create($value);
        }
    }
}
