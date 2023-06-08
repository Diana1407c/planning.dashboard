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
        Schema::rename('people', 'engineers');

        Schema::table('team_lead_planning', function (Blueprint $table) {
            $table->dropForeign(['person_id']);
            $table->renameColumn('person_id', 'engineer_id');

            $table->foreign('engineer_id')
                ->references('id')
                ->on('engineers')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('engineers', 'people');

        Schema::table('team_lead_planning', function (Blueprint $table) {
            $table->dropForeign(['engineer_id']);
            $table->renameColumn('engineer_id', 'person_id');

            $table->foreign('person_id')
                ->references('id')
                ->on('people')
                ->onDelete('cascade');
        });
    }
};
