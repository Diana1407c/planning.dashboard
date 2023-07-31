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
        Schema::create('engineer_performances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('engineer_id');
            $table->unsignedBigInteger('level_id');
            $table->integer('performance')->nullable();
            $table->date('from_date');
            $table->boolean('is_current')->default(0);
            $table->timestamps();

            $table->foreign('level_id')
                ->references('id')
                ->on('levels')
                ->onDelete('cascade');

            $table->foreign('engineer_id')
                ->on('engineers')
                ->references('id')
                ->onDelete('cascade');
        });

        Schema::table('engineers', function (Blueprint $table) {
            $table->dropForeign(['level_id']);
            $table->dropColumn('level_id');
            $table->dropColumn('performance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('engineer_performances');

        Schema::table('engineers', function (Blueprint $table) {
            $table->unsignedBigInteger('level_id')->nullable()->after('username');
            $table->tinyInteger('performance')->nullable()->after('level_id');;
            $table->foreign('level_id')
                ->references('id')
                ->on('levels')
                ->onDelete('cascade');
        });
    }
};
