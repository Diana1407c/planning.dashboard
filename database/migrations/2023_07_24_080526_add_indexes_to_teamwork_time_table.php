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
        Schema::table('teamwork_time', function (Blueprint $table) {
            $table->index('date');
            $table->index('billable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teamwork_time', function (Blueprint $table) {
            $table->dropIndex(['date']);
            $table->dropIndex(['billable']);
        });
    }
};
