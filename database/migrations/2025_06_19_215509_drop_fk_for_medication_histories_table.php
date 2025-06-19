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
            $table->dropForeign('medication_histories_user_id_foreign');
            $table->dropIndex('medication_histories_user_id_foreign');;
            $table->index('user_id');
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
