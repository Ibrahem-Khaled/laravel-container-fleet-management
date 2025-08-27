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
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('driver_id')->nullable()->unique();
            $table->enum('type', ['transfer', 'private']);
            $table->string('type_car')->nullable();
            $table->bigInteger('model_car')->nullable();
            $table->bigInteger('serial_number')->unique();
            $table->string('license_expire')->nullable();
            $table->string('scan_expire')->nullable();
            $table->string('card_run_expire')->nullable();
            $table->string('number')->unique();
            $table->string('insurance_expire')->nullable();
            $table->string('oil_change_number')->default(10000)->nullable();
            $table->softDeletes();

            $table->timestamps();

            $table->foreign('driver_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
