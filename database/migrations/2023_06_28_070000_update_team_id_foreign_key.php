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
        Schema::table('engineers', function (Blueprint $table) {
            $table->dropForeign('people_team_id_foreign');
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('set null');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('engineers', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
            $table->foreign('team_id', 'people_team_id_foreign')->references('id')->on('teams')->onDelete('cascade');
        });
    }
};
