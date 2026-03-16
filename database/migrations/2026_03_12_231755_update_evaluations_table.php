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
        Schema::table('evaluations', function (Blueprint $table) {
            $table->text('strengths')->nullable()->after('evaluation_text');
            $table->text('weaknesses')->nullable()->after('strengths');
            $table->text('recommendations')->nullable()->after('weaknesses');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evaluations', function (Blueprint $table) {
            $table->dropColumn(['strengths', 'weaknesses', 'recommendations']);
        });
    }
};
