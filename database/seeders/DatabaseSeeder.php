<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::create([
            'username' => 'admin',
            'name' => 'Admin',
            'posisi' => 'Staff',
            'email' => 'admin@mail.com',
            'password' => bcrypt('diskominfo'),
            'type_user' => 'admin',
            'instansi' => 'DISKOMINFO'
        ]);
        User::create([
            'username' => 'konsultan',
            'name' => 'vendor',
            'posisi' => 'vendor',
            'email' => 'vendor@mail.com',
            'password' => bcrypt('diskominfo'),
            'type_user' => 'konsultan',
            'instansi' => 'vendor'
        ]);
        User::create([
            'username' => 'petugas',
            'name' => 'petugas',
            'posisi' => 'petugas',
            'email' => 'petugas@mail.com',
            'password' => bcrypt('diskominfo'),
            'type_user' => 'admin',
            'instansi' => 'DISKOMINFO'
        ]);
        User::create([
            'username' => 'pelapor',
            'name' => 'pelapor',
            'posisi' => 'pelapor',
            'email' => 'pelapor@mail.com',
            'password' => bcrypt('diskominfo'),
            'type_user' => 'pelapor',
            'instansi' => 'DISKOMINFO'
        ]);
    }
}
