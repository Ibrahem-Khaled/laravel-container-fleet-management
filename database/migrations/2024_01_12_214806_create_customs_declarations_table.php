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
        Schema::create('customs_declarations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('statement_number');
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('clearance_office_id');
            $table->date('expire_date')->nullable();
            $table->bigInteger('weight')->default(0)->nullable();
            $table->text('statement_status')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('clearance_office_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customs_declarations');
    }
};
