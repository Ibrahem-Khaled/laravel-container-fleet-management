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
        Schema::create('container_transfer_orders', function (Blueprint $table) {
            $table->id();
            // كل أمر مرتبط بحاوية
            $table->foreignId('container_id')
                ->constrained('containers')
                ->cascadeOnDelete();
            // السعر/القيمة (دقة عشرية كافية)
            $table->decimal('price', 18, 2);
            // ملاحظات اختيارية
            $table->text('note')->nullable();
            // تاريخ تنفيذ/تحرير الأمر
            $table->timestamp('ordered_at')->useCurrent();
            $table->timestamps();
            // فهارس مفيدة للاستعلامات الشائعة
            $table->index(['container_id', 'ordered_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('container_transfer_orders');
    }
};
