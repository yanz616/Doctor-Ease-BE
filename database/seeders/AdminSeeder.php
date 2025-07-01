<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Administrator',
                'avatar' => 'https://i.pinimg.com/1200x/29/86/1e/29861ea3420bde3ce1dcea7051564c04.jpg',
                'password' => Hash::make('sayaAdmin'),
                'is_admin' => true,
            ]
        );
    }
}
