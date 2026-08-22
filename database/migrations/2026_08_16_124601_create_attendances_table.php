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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            // user_type will store 'employee' or 'manager' depending on the role
            $table->string('user_type');
            $table->unsignedBigInteger('user_id');
            $table->date('date');
            $table->timestamp('check_in')->nullable();
            $table->timestamp('check_out')->nullable();
            // status can be: present, late, absent, on_leave
            $table->string('status')->default('present');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Allow polymorphic relations
            $table->index(['user_type', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
