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
        Schema::table('hr_users', function (Blueprint $table) {
            $table->string('role')->default('hr')->after('email'); // roles: gm (general manager), hr (hr specialist)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hr_users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
