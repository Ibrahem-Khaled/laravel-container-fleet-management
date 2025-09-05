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
        Schema::create('profit_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profit_run_id')->constrained()->cascadeOnDelete();
            $table->foreignId('partner_id')->constrained()->cascadeOnDelete();
            $table->decimal('weight_capital_days', 24, 8); // مجموع (الرصيد × عدد الأيام)
            $table->decimal('share_amount', 18, 4);        // نصيب الشريك من ربح الشهر
            $table->decimal('avg_balance_during_period', 18, 4)->default(0); // اختياري
            $table->timestamps();

            $table->unique(['profit_run_id', 'partner_id']);
            $table->index(['partner_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profit_allocations');
    }
};
