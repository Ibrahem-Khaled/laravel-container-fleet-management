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
        Schema::table('users', function (Blueprint $table) {
            // تحديث حقل salary ليقبل القيم الفارغة مع قيمة افتراضية 0
            $table->decimal('salary', 10, 2)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // إرجاع الحقل إلى حالته الأصلية
            $table->decimal('salary', 10, 2)->nullable(false)->change();
        });
    }
};
