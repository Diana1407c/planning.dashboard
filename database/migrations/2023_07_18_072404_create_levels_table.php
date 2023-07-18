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
        Schema::create('levels', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('engineer_id')->unique();
            $table->enum('name', ['junior', 'middle', 'senior']);
            $table->tinyInteger('performance')->comment('Performance in percentage');
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
        Schema::dropIfExists('levels');
    }
};
