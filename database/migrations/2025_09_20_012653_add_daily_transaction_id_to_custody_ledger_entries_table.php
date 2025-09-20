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
        Schema::table('custody_ledger_entries', function (Blueprint $table) {
            // إضافة العمود إذا لم يكن موجوداً
            if (!Schema::hasColumn('custody_ledger_entries', 'daily_transaction_id')) {
                $table->foreignId('daily_transaction_id')->nullable()
                    ->constrained('daily_transactions')->nullOnDelete()
                    ->after('custody_account_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('custody_ledger_entries', function (Blueprint $table) {
            $table->dropForeign(['daily_transaction_id']);
            $table->dropColumn('daily_transaction_id');
        });
    }
};
