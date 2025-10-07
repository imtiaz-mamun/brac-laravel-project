<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            BranchSeeder::class,
            ClientSeeder::class,
            LoanSeeder::class,
            RepaymentSeeder::class,
        ]);
    }
}