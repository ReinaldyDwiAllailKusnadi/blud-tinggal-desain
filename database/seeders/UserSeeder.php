<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(['email' => 'user@ex.com'], [
            'username' => 'quinlan',
            'name' => 'Quinlan Kessler',
            'email' => 'user@ex.com',
            'phone' => '081234567890',
            'password' => Hash::make('user123'),
        ]);
    }
}
