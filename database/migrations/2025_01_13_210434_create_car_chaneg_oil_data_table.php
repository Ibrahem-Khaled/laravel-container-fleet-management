<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('car_chaneg_oil_data', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('car_id');
            $table->bigInteger('km_before')->default(0);
            $table->bigInteger('km_after')->default(0);
            $table->bigInteger('price')->default(0);
            $table->date('date')->nullable();
            $table->softDeletes();

            $table->timestamps();
            $table->foreign('car_id')->references('id')->on('cars')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_chaneg_oil_data');
    }
};
