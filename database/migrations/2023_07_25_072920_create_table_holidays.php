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
        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('name')->nullable();
            $table->enum('type', ['holiday', 'day_off', 'short', 'recoverable'])->default('holiday');
            $table->unsignedTinyInteger('day_hours')->nullable();
            $table->boolean('every_year')->default(0);
            $table->timestamps();
        });

        $this->createPermissions();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('holidays');
    }

    protected function createPermissions(): void
    {
        $permission = \App\Models\Permission::firstOrCreate([
            'name' => 'manage holidays',
            'guard_name' => 'web',
        ]);

        $roles = \App\Models\Role::query()->whereIn('name', ['admin', 'project_manager', 'accountant'])->get();

        foreach ($roles as $role) {
            $role->givePermissionTo($permission);
        }
    }
};
