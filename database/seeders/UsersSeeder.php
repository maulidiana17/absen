<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'tina',
            'email' => 'tina@gmail.com',
            'password' => Hash::make('112233445566'),
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

    }
}
