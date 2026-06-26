<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'anton.fullstack@gmail.com'],
            [
                'name' => 'Anton',
                'password' => config('admin.password'),
            ],
        );
    }
}
