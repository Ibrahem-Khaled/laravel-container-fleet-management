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
        Schema::create('office_tax_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('office_id')->constrained('users')->onDelete('cascade');
            $table->boolean('tax_enabled')->comment('حالة الضرائب: true = مفعلة, false = معطلة');
            $table->timestamp('effective_from')->comment('تاريخ بداية التفعيل/الإلغاء');
            $table->timestamp('effective_to')->nullable()->comment('تاريخ نهاية التفعيل/الإلغاء (null = مستمر)');
            $table->decimal('tax_rate', 5, 2)->default(15.00)->comment('نسبة الضريبة (15%)');
            $table->text('notes')->nullable()->comment('ملاحظات إضافية');
            $table->foreignId('changed_by')->constrained('users')->comment('المستخدم الذي قام بالتغيير');
            $table->timestamps();
            
            $table->index(['office_id', 'effective_from']);
            $table->index(['office_id', 'effective_to']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('office_tax_histories');
    }
};
