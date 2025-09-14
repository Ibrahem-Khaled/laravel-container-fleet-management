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
        Schema::create('custody_accounts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->decimal('opening_balance', 18, 2)->default(0);

            // open/closed
            $table->enum('status', ['open', 'closed'])->default('open');

            // من سلّم/أقفل العهدة
            $table->foreignId('opened_by')->nullable()
                ->constrained('users')->nullOnDelete();
            $table->foreignId('closed_by')->nullable()
                ->constrained('users')->nullOnDelete();

            $table->timestamp('opened_at')->nullable();
            $table->timestamp('closed_at')->nullable();

            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custody_accounts');
    }
};
