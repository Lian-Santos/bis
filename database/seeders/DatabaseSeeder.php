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
        db::statement("INSERT
        INTO roles
        (role_type)
        Values
        ('user'),('admin'),('super_admin')
        ");
    }
}
