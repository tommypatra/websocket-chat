<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        //untuk pengguna
        User::create([
            'name' => 'Admin App',
            'email' => 'admin@thisapp.com', //email login
            'password' => Hash::make('00000000'), // password default login 
        ]);

        for ($i = 1; $i <= 5; $i++) {
            User::create([
                'name' => 'Pengguna ' . $i,
                'email' => 'pengguna' . $i . '@thisapp.com', //email login
                'password' => Hash::make('00000000'), // password default login 
            ]);
        }
    }
}
