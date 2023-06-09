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
        Schema::table('project_manager_planning', function (Blueprint $table) {
            $table->dropForeign(['technology_id']);
            $table->dropColumn('technology_id');

            $table->unsignedBigInteger('stack_id')->after('project_id');

            $table->foreign('stack_id')
                ->references('id')
                ->on('stacks')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_manager_planning', function (Blueprint $table) {
            $table->dropForeign(['stack_id']);
            $table->dropColumn('stack_id');

            $table->unsignedBigInteger('technology_id')->after('project_id');

            $table->foreign('technology_id')
                ->references('id')
                ->on('technologies')
                ->onDelete('cascade');
        });
    }
};
