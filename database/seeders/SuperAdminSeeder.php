<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@jobflow.com'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('password'), // Change this in production!
                'is_super_admin' => true,
            ]
        );
    }
}
