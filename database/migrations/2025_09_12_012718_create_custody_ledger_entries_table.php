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
        Schema::create('custody_ledger_entries', function (Blueprint $table) {
            $table->id();

            $table->foreignId('custody_account_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            // لربط سطر الدفتر بسطر اليومية (اختياري) — FK عادي مش مورف
            $table->foreignId('daily_transaction_id')->nullable()
                ->constrained('daily_transactions')->nullOnDelete();

            // issue: تسليم عهدة / return: توريد / expense: مصروف / income: تحصيل
            // adjustment: تسوية / transfer_*: تحويل بين عهد
            $table->enum('direction', [
                'issue',
                'return',
                'expense',
                'income',
                'adjustment',
                'transfer_out',
                'transfer_in'
            ]);

            $table->decimal('amount', 18, 2);
            $table->char('currency', 3)->nullable();
            $table->timestamp('occurred_at')->useCurrent();

            // مرجع اختياري (فاتورة/طلب… إلخ) — بدون مورف هنا
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('reference_type')->nullable();

            // طرف مقابل
            $table->foreignId('counterparty_user_id')->nullable()
                ->constrained('users')->nullOnDelete();

            $table->foreignId('created_by')->nullable()
                ->constrained('users')->nullOnDelete();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['custody_account_id', 'occurred_at']);
            $table->index(['direction']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custody_ledger_entries');
    }
};
