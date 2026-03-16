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
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->unsignedBigInteger('plan_id')->nullable()->after('company_id');
            $table->unsignedBigInteger('hr_user_id')->nullable()->after('plan_id');
            $table->date('start_date')->nullable()->after('hr_user_id');
            $table->date('end_date')->nullable()->after('start_date');
            $table->string('status')->default('active')->after('end_date');

            $table->foreign('plan_id', 'cas_subscriptions_plan_id_foreign')->references('id')->on('plans')->nullOnDelete();
            $table->foreign('hr_user_id', 'cas_subscriptions_hr_user_id_foreign')->references('id')->on('hr_users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropForeign('cas_subscriptions_plan_id_foreign');
            $table->dropForeign('cas_subscriptions_hr_user_id_foreign');
            $table->dropColumn(['plan_id', 'hr_user_id', 'start_date', 'end_date', 'status']);
        });
    }
};
