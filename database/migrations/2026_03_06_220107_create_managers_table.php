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
        Schema::create('managers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('password');
            $table->date('hire_date')->nullable();
            $table->integer('tasks_completed')->default(0);
            $table->integer('tasks_requested')->default(0);
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->enum('role', ['general_manager', 'department_manager']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('managers');
    }
};
