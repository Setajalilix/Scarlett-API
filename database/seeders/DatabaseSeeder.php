<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::create(
            [
                'username' => 'admin',
                'role' => 'admin',
                'password' => Hash::make('1234'),
            ],
            [
                'username' => 'user',
                'role' => 'user',
                'password' => Hash::make('1234'),
            ]);
    }
}
