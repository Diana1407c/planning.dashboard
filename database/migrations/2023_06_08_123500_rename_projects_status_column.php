<?php

use App\Models\Project;
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
        Schema::table('projects', function (Blueprint $table) {
            $table->enum('state', [
                Project::STATE_ACTIVE,
                Project::STATE_MAINTENANCE,
                Project::STATE_OPERATIONAL
            ])->default(Project::STATE_ACTIVE)->after('name');
            $table->dropColumn('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('state')->nullable()->after('name');
            $table->dropColumn('state');
        });
    }
};
