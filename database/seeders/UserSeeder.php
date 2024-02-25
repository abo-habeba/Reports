<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = DB::table('users')->where('email', 'admin@admin.com')->first();

        if (!$user) {
            $userId = DB::table('users')->insertGetId([
                'name' => 'admin',
                'phone' => '01234567891',
                'roles' => 'admin',
                'created_at' => now(),
                'Job_title' => 'engeneer',
                'email' => 'admin@admin.com',
                'password' => Hash::make('12345678'),
            ]);

            DB::table('rest_balances')->insert([
                'balance' => 0,
                'user_id' => $userId,
                'created_at' => now(),
            ]);

            DB::table('regular_balances')->insert([
                'balance' => 0,
                'user_id' => $userId,
                'created_at' => now(),
            ]);
        }
    }
}
