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
        Schema::create('tips', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('container_id')->nullable();
            $table->unsignedBigInteger('driver_id')->nullable();
            $table->unsignedBigInteger('car_id')->nullable();
            $table->integer('price')->nullable();
            $table->string('type')->nullable()->default('empty');
            $table->softDeletes();
            $table->timestamps();
            $table->unique(['container_id', 'driver_id', 'car_id', 'deleted_at'], 'tips_unique_combo');

            $table->foreign('container_id')->references('id')->on('containers')->onDelete('cascade');
            $table->foreign('driver_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('car_id')->references('id')->on('cars')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tips', function (Blueprint $table) {
            $table->dropUnique('tips_unique_combo');
        });
    }
};
