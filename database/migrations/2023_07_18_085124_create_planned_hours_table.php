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
        Schema::create('planned_hours', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->enum('planable_type', ['technology', 'engineer']);
            $table->unsignedBigInteger('planable_id');
            $table->integer('year');
            $table->integer('period_number');
            $table->enum('period_type', ['week', 'month']);
            $table->integer('hours');

            $table->foreign('project_id')
                ->references('id')
                ->on('projects')
                ->onDelete('cascade');

            $table->index(['planable_type', 'planable_id']);
            $table->index(['period_type', 'year', 'period_number']);

            $table->timestamps();
        });

        Schema::dropIfExists('pm_planning_prices');
        Schema::dropIfExists('team_lead_planning');
        Schema::dropIfExists('project_manager_planning');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planned_hours');
    }
};
