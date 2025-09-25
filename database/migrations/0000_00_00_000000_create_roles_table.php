<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });


        DB::table('roles')->insert([
            [
                'name' => 'client',
                'description' => 'عميل',
                'created_at' => now(),
            ],
            [
                'name' => 'admin',
                'description' => 'ادمن',
                'created_at' => now(),
            ],
            [
                'name' => 'clearance_office',
                'description' => 'مكتب تخليص جمركي',
                'created_at' => now(),
            ],
            [
                'name' => 'employee',
                'description' => 'موظف',
                'created_at' => now(),
            ],
            [
                'name' => 'driver',
                'description' => 'سائق',
                'created_at' => now(),
            ],
            [
                'name' => 'partner',
                'description' => 'شريك',
                'created_at' => now(),
            ],
            [
                'name' => 'accountant',
                'description' => 'محاسب',
                'created_at' => now(),
            ],

        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
