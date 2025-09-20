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
        Schema::table('daily_transactions', function (Blueprint $table) {
            // إضافة حقل تاريخ الحركة المخصص
            if (!Schema::hasColumn('daily_transactions', 'transaction_date')) {
                $table->timestamp('transaction_date')->nullable()
                    ->after('notes')
                    ->comment('تاريخ الحركة المخصص - إذا كان فارغاً يستخدم created_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_transactions', function (Blueprint $table) {
            $table->dropColumn('transaction_date');
        });
    }
};
