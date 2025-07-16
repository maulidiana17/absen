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
            'name' => 'admin',
            'email' => 'dinii@gmail.com',
            'password' => Hash::make('dini123'),
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Guru 1
        User::create([
            'name' => 'Suluh Setya H., S.Pd',
            'email' => 'suluhsetyah.@sekolah.sch.id',
            'password' => Hash::make('password1'),
            'role' => 'guru',
            'created_at' => now(),
            'updated_at' => now(),
        ]);


    }
}
