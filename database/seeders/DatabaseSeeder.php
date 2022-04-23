<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            AdminUsersSeeder::class,
            DrugSeeder::class,
            MedicationHistory::class,
            UsersSeeder::class,
        ]);

    }
}
