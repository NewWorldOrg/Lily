<?php

namespace Database\Seeders;

use Domain\Common\CreatedAt;
use Domain\Common\UpdatedAt;
use Domain\MedicationHistory\MedicationDate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MedicationHistory extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('medication_histories')->insert([
            'user_id' => 930316,
            'drug_id' => 1,
            'amount' => 2,
            'medication_date' => MedicationDate::now()->getSqlTimeStamp(),
            'created_at' => CreatedAt::now()->getSqlTimeStamp(),
            'updated_at' => UpdatedAt::now()->getSqlTimeStamp(),
        ]);

        DB::table('medication_histories')->insert([
            'user_id' => 930316,
            'drug_id' => 2,
            'amount' => 10,
            'medication_date' => MedicationDate::now()->getSqlTimeStamp(),
            'created_at' => CreatedAt::now()->getSqlTimeStamp(),
            'updated_at' => UpdatedAt::now()->getSqlTimeStamp(),
        ]);
    }
}
