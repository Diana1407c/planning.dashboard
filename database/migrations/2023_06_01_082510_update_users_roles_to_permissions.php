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
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
            $table->boolean('is_admin')->default(false)->after('person_id');
            $table->boolean('is_project_manager')->default(false)->after('person_id');
            $table->boolean('is_team_lead')->default(false)->after('person_id');
            $table->boolean('is_accountant')->default(true)->after('person_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_admin');
            $table->dropColumn('is_project_manager');
            $table->dropColumn('is_team_lead');
            $table->dropColumn('is_accountant');
            $table->enum('role', ['admin', 'project_manager', 'team_lead', 'accountant']);
        });
    }
};
