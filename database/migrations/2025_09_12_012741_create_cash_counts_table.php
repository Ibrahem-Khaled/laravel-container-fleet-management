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
        Schema::create('cash_counts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('custody_account_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('counted_by')->nullable()
                ->constrained(table: 'users')
                ->nullOnDelete();

            $table->timestamp('counted_at')->useCurrent();

            $table->decimal('total_expected', 18, 2)->default(0);
            $table->decimal('total_counted', 18, 2)->default(0);
            $table->decimal('difference', 18, 2)->default(0);

            $table->enum('status', ['draft', 'posted'])->default('draft');

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['custody_account_id', 'counted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_counts');
    }
};
