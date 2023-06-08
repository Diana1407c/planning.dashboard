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
        Schema::table('technologies', function (Blueprint $table) {
            $table->unsignedBigInteger('stack_id')->after('name');

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
        Schema::table('technologies', function (Blueprint $table) {
            $table->dropForeign(['stack_id']);
            $table->dropColumn('stack_id');
        });
    }
};
