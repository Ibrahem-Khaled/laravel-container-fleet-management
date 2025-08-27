<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('containers', function (Blueprint $table) {
            $table->id();
            // this relations
            $table->unsignedBigInteger('rent_id')->nullable();
            $table->unsignedBigInteger('customs_id');

            $table->boolean('is_rent')->default(0);
            $table->string('number');
            $table->enum('size', ['20', '40', 'box'])->default('20');
            $table->bigInteger('price')->default(0);
            $table->bigInteger('rent_price')->default(0);
            $table->enum('status', ['wait', 'transport', 'done', 'rent', 'storage'])->default('wait');
            $table->timestamp('transfer_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('date_empty')->nullable();
            $table->text('direction')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('rent_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('customs_id')->references('id')->on('customs_declarations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('containers');
    }
};
