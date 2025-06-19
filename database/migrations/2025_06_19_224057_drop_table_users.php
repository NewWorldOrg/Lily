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
        Schema::table('user_definitive_register_tokens', function (Blueprint $table) {
            $table->dropForeign('user_definitive_register_tokens_user_id_foreign');
        });
        Schema::drop('users');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
