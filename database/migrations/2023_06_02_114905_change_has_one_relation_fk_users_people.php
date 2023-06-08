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
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['person_id']);
            $table->dropColumn('person_id');
        });

        Schema::table('people', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('team_id');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('people', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('person_id')->nullable()->after('remember_token');

            $table->foreign('person_id')
                ->references('id')
                ->on('people')
                ->onDelete('cascade');
        });
    }
};
