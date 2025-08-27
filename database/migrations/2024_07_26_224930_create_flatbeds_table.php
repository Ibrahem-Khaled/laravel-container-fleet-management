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
        Schema::create('flatbeds', function (Blueprint $table) {
            $table->id();
            $table->string('number')->nullable()->autoIncrement();
            $table->enum('type', ['20', '40', 'box'])->nullable();
            $table->text('description')->nullable();
            $table->text('image')->nullable();
            $table->boolean('status')->default(1);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flatbeds');
    }
};
