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
        Schema::create('alhamdulillah_expenses', function (Blueprint $table) {
            $table->id();
            $table->integer('year');
            $table->integer('month');
            $table->integer('container_count');
            $table->decimal('amount_per_container', 10, 2);
            $table->decimal('total_amount', 10, 2);
            $table->decimal('spent_amount', 10, 2)->default(0);
            $table->decimal('remaining_amount', 10, 2);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['year', 'month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alhamdulillah_expenses');
    }
};
