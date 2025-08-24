<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('medication_histories', function (Blueprint $table) {
            $table->dateTime('medication_date')->nullable()->after('amount');
        });


        \Infra\EloquentModels\MedicationHistory::all()->each(
            function (\Infra\EloquentModels\MedicationHistory $model) {
                $model->medication_date = $model->created_at;
                $model->save();
            }
        );

        Schema::table('medication_histories', function (Blueprint $table) {
            $table->dateTime('medication_date')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medication_histories', function (Blueprint $table) {
            //
        });
    }
};
