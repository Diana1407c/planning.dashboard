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
        Schema::create('engineer_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('engineer_id');
            $table->string('historyable_type');
            $table->unsignedBigInteger('historyable_id')->nullable();
            $table->text('value')->nullable();
            $table->string('label');
            $table->timestamps();

            $table->foreign('engineer_id')->references('id')->on('engineers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('engineer_histories');
    }
};
