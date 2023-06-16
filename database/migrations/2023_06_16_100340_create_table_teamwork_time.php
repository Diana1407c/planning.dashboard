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
        Schema::create('teamwork_time', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('engineer_id');
            $table->float('hours');
            $table->date('date');
            $table->boolean('billable');
            $table->timestamps();

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
        Schema::dropIfExists('teamwork_time');
    }
};
