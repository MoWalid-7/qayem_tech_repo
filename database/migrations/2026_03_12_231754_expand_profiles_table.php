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
        Schema::table('employees', function (Blueprint $table) {
            $table->date('hire_date')->nullable()->after('job_title');
            $table->integer('tasks_requested')->default(0)->after('tasks_completed');
        });

        Schema::table('managers', function (Blueprint $table) {
            $table->date('hire_date')->nullable()->after('password');
            $table->integer('tasks_completed')->default(0)->after('hire_date');
            $table->integer('tasks_requested')->default(0)->after('tasks_completed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['hire_date', 'tasks_requested']);
        });

        Schema::table('managers', function (Blueprint $table) {
            $table->dropColumn(['hire_date', 'tasks_completed', 'tasks_requested']);
        });
    }
};
