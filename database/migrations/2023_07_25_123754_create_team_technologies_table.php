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
        Schema::create('team_technology', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->unsignedBigInteger('technology_id');
            $table->foreign('team_id')
                ->references('id')
                ->on('teams')
                ->onDelete('cascade');
            $table->foreign('technology_id')
                ->references('id')
                ->on('technologies')
                ->onDelete('cascade');
            $table->timestamps();
        });

        $this->migrateData();

        Schema::table('teams', function (Blueprint $table) {
            $table->dropForeign(['technology_id']);
            $table->dropColumn('technology_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_technology');

        Schema::table('teams', function (Blueprint $table) {
            $table->unsignedBigInteger('technology_id')->nullable();
            $table->foreign('technology_id')->references('id')->on('technologies')
                ->onDelete('set null');
        });
    }

    protected function migrateData()
    {
        $teams = \App\Models\Team::all();

        foreach ($teams as $team) {
            $team->technologies()->sync([$team->technology_id]);
        }
    }
};
