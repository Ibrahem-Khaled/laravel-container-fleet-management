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
        Schema::create('flatbed_containers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('container_id')->unsigned();
            $table->bigInteger('flatbed_id')->unsigned();

            $table->foreign('container_id')->references('id')->on('containers');
            $table->foreign('flatbed_id')->references('id')->on('flatbeds');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flatbed_containers');
    }
};
