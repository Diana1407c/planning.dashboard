<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('planned_hours', function (Blueprint $table) {
            $table->integer('performance_hours')->nullable();
        });

        \App\Models\PlannedHour::query()->where('planable_type', 'engineer')
            ->update([
                'performance_hours' => \Illuminate\Support\Facades\DB::raw('hours')
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('planned_hours', function (Blueprint $table) {
            $table->dropColumn('performance_hours');
        });
    }
};
