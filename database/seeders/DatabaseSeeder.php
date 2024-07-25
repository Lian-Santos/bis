<?php

namespace Database\Seeders;
use DB;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        /* db::statement("INSERT
        INTO roles
        (role_type)
        Values
        ('user'),('admin'),('super_admin')
        "); */
        for ($x = 0; $x <= 10; $x++) {
        $user_id = DB::table('users')->insertGetId([
            'first_name' => fake()->firstName(),
            'middle_name' => fake()->lastName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'password' => password_hash(123, PASSWORD_DEFAULT),
        ]);
        DB::table('user_roles')
            ->insert([
                'user_id' => $user_id,
                'role_id' => 1
            ]);
        }
    }
}
