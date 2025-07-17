<?php

namespace Database\Seeders;

use App\Models\Guru;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserRoleSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan role sudah ada
        $roleAdmin = Role::firstOrCreate(['name' => 'admin']);
        $roleGuru = Role::firstOrCreate(['name' => 'guru']);

        // Admin
        $admin = User::firstOrCreate(
            ['email' => 'tari@example.com'],
            ['name' => 'tari', 'password' => Hash::make('123456')]
        );
        $admin->assignRole($roleAdmin);

        // Guru
        $guruUser = User::firstOrCreate(
            ['email' => 'guru@example.com'],
            ['name' => 'guru', 'password' => Hash::make('123456')]
        );
        $guruUser->assignRole($roleGuru);

        Guru::firstOrCreate([
            'user_id' => $guruUser->id,
            'nip' => '1234567890',
            'kode_guru' => 'AB10',
            'mapel' => 'guru@example.com',
            'alamat' => 'sawahan',
        ]);

    }
}
