<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Role;
use App\Models\OfficeTaxHistory;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // إنشاء سجل الضرائب الأولي للمكاتب الموجودة
        $clearanceOfficeRole = Role::where('name', 'clearance_office')->first();

        if ($clearanceOfficeRole) {
            $offices = User::where('role_id', $clearanceOfficeRole->id)->get();

            foreach ($offices as $office) {
                // التحقق من عدم وجود سجل ضريبي للمكتب
                $existingRecord = OfficeTaxHistory::where('office_id', $office->id)->first();

                if (!$existingRecord) {
                    // إنشاء سجل ضريبي أولي
                    OfficeTaxHistory::create([
                        'office_id' => $office->id,
                        'tax_enabled' => $office->tax_enabled ?? true,
                        'effective_from' => $office->created_at ?? now(),
                        'effective_to' => null,
                        'tax_rate' => 15.00,
                        'notes' => 'سجل ضريبي أولي للمكتب الموجود',
                        'changed_by' => 1, // Admin user
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // حذف السجلات الأولية للضرائب
        OfficeTaxHistory::where('notes', 'سجل ضريبي أولي للمكتب الموجود')->delete();
    }
};
