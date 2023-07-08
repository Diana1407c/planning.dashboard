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
        Schema::table('teams', function (Blueprint $table) {
            $table->dropForeign(['team_lead_id']);
            $table->unsignedBigInteger('team_lead_id')->nullable()->change();
            $table->foreign('team_lead_id')->references('id')->on('engineers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropForeign('teams_team_lead_id_foreign');
            $table->foreign('team_lead_id')->references('id')->on('teams')->onDelete('cascade');
        });
    }
};
