<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        User::factory()
            ->count(50)
            ->create();

        User::create([
            'name' => 'nguyen',
            'email' => 'nguyenpham16112k3@gmail.com',
            'username' => 'phamnham',
            'phone_number' => '0987654321',
            'password' => Hash::make('password'),
        ]);
    }
}
