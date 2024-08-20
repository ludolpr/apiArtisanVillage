<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'user_name' => 'ludolpr',
            'password' => Hash::make('Azertyuiop1$'),
            'email' => 'ludolpr@gmail.com',
            'email_verified_at' => now(),
            'user_picture' => 'user.jpg',
            'remember_token' => Str::random(10),
            'id_role' => 2,
        ]);
        // Création de 8 utilisateurs aléatoires
        User::factory(8)->create();
    }
}
