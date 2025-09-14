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
        Schema::create('daily_transactions', function (Blueprint $table) {
            $table->id();
            // هذا السطر هو مفتاح العلاقة المتعددة الأشكال
            $table->morphs('transactionable'); // سيقوم بإنشاء transactionable_id و transactionable_type
            $table->foreignId('custody_account_id')->nullable();
            $table->enum('type', ['income', 'expense']); // نوع الحركة: وارد أو منصرف
            $table->enum('method', ['cash', 'bank']); // طريقة الحركة
            $table->decimal('amount', 15, 2); // المبلغ الأساسي
            $table->decimal('tax_value', 15, 2)->default(0); // قيمة الضريبة
            $table->decimal('total_amount', 15, 2); // المبلغ الإجمالي
            $table->text('notes')->nullable(); // ملاحظات
            $table->softDeletes();
            $table->timestamps();
            // $table->index(['transactionable_type', 'transactionable_id']);
            $table->index(['custody_account_id', 'type', 'method']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_transactions');
    }
};
