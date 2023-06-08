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
        Schema::table('team_lead_planning', function (Blueprint $table) {
            $table->dropColumn('hours');
            $table->integer('time')->default(0)->after('week');
        });

        Schema::table('project_manager_planning', function (Blueprint $table) {
            $table->dropColumn('hours');
            $table->integer('time')->default(0)->after('week');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('team_lead_planning', function (Blueprint $table) {
            $table->dropColumn('time');
            $table->float('hours');
        });

        Schema::table('project_manager_planning', function (Blueprint $table) {
            $table->dropColumn('minutes');
            $table->float('hours')->change();
        });
    }
};
