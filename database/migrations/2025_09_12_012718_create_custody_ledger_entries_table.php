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

            // issue: تسليم عهدة / return: توريد / expense: مصروف / income: تحصيل
            // adjustment: تسوية جرد / transfer_*: تحويل بين مستخدمين
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
            $table->char('currency', 3)->nullable(); // إن رغبت بدعم عملات

            $table->timestamp('occurred_at')->useCurrent();

            // الحركة مرتبطة بمصدر (فاتورة/مصروف/طلب...) - polymorphic
            $table->nullableMorphs('reference');

            // الجهة المقابلة (مستخدم آخر) - في التحويلات مثلًا
            $table->foreignId('counterparty_user_id')->nullable()
                ->constrained(table: 'users')
                ->nullOnDelete();

            $table->foreignId('created_by')->nullable()
                ->constrained(table: 'users')
                ->nullOnDelete();

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
