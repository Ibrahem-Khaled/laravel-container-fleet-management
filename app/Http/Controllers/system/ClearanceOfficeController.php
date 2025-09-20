<?php

namespace App\Http\Controllers\system;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Models\OfficeTaxHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ClearanceOfficeController extends Controller
{
    public function index(Request $request)
    {
        $clearanceOfficeRole = Role::where('name', 'clearance_office')->firstOrFail();

        $query = User::where('role_id', $clearanceOfficeRole->id);

        // فلترة البحث
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $offices = $query->latest()->paginate(10);

        // إحصائيات
        $stats = [
            'total_offices' => User::where('role_id', $clearanceOfficeRole->id)->count(),
            'active_offices' => User::where('role_id', $clearanceOfficeRole->id)->where('is_active', true)->count(),
            'inactive_offices' => User::where('role_id', $clearanceOfficeRole->id)->where('is_active', false)->count(),
            'tax_enabled_offices' => User::where('role_id', $clearanceOfficeRole->id)->where('tax_enabled', true)->count(),
            'tax_disabled_offices' => User::where('role_id', $clearanceOfficeRole->id)->where('tax_enabled', false)->count(),
        ];

        return view('dashboard.clearance_offices.index', compact('offices', 'stats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:20|unique:users,phone',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'required|boolean',
            'tax_enabled' => 'required|boolean',
            'operational_number' => 'nullable|integer|unique:users,operational_number',
        ]);

        $clearanceOfficeRole = Role::where('name', 'clearance_office')->firstOrFail();
        $validated['role_id'] = $clearanceOfficeRole->id;

        // لا نطلب كلمة مرور، يمكن تركها فارغة
        // $validated['password'] = Hash::make(Str::random(10));

        if ($request->hasFile('avatar')) {
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $office = User::create($validated);

        // إنشاء سجل الضرائب الأولي
        OfficeTaxHistory::createNewTaxPeriod(
            $office->id,
            $validated['tax_enabled'],
            now(),
            15.00,
            'إنشاء مكتب جديد',
            auth()->id()
        );

        return redirect()->route('clearance-offices.index')->with('success', 'تمت إضافة مكتب التخليص بنجاح.');
    }

    public function update(Request $request, User $clearance_office)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['nullable', 'string', 'email', 'max:255', Rule::unique('users')->ignore($clearance_office->id)],
            'phone' => ['nullable', 'string', 'max:20', Rule::unique('users')->ignore($clearance_office->id)],
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'required|boolean',
            'tax_enabled' => 'required|boolean',
            'operational_number' => ['nullable', 'integer', Rule::unique('users')->ignore($clearance_office->id)],
        ]);

        if ($request->hasFile('avatar')) {
            // Optional: Delete old avatar
            // Storage::disk('public')->delete($clearance_office->avatar);
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $clearance_office->update($validated);

        // إذا تغيرت حالة الضرائب، احفظ السجل
        if ($clearance_office->wasChanged('tax_enabled')) {
            OfficeTaxHistory::createNewTaxPeriod(
                $clearance_office->id,
                $validated['tax_enabled'],
                now(),
                15.00,
                'تعديل حالة الضرائب من خلال التعديل',
                auth()->id()
            );
        }

        return redirect()->route('clearance-offices.index')->with('success', 'تم تعديل بيانات المكتب بنجاح.');
    }

    public function destroy(User $clearance_office)
    {
        // Soft Delete
        $clearance_office->delete();

        return redirect()->route('clearance-offices.index')->with('success', 'تم حذف المكتب (نقله إلى الأرشيف).');
    }

    public function show(User $clearance_office)
    {
        // Eager load relationships for efficiency
        $clearance_office->load(['customsDeclarations' => function ($query) {
            $query->with(['client', 'containers'])->latest();
        }]);

        return view('dashboard.clearance_offices.show', compact('clearance_office'));
    }

    /**
     * تفعيل أو إلغاء تفعيل الضرائب لمكتب معين
     */
    public function toggleTax(User $clearance_office)
    {
        // التأكد من أن المستخدم مكتب تخليص جمركي
        if (!$clearance_office->isClearanceOffice()) {
            return back()->with('error', 'المستخدم المحدد ليس مكتب تخليص جمركي.');
        }

        $oldTaxStatus = $clearance_office->tax_enabled;
        $clearance_office->update(['tax_enabled' => !$clearance_office->tax_enabled]);

        // حفظ سجل التغيير
        OfficeTaxHistory::createNewTaxPeriod(
            $clearance_office->id,
            !$oldTaxStatus,
            now(),
            15.00,
            'تبديل حالة الضرائب من خلال الزر',
            auth()->id()
        );

        $status = $clearance_office->tax_enabled ? 'مفعلة' : 'معطلة';
        return back()->with('success', "تم {$status} الضرائب للمكتب {$clearance_office->name}.");
    }

    /**
     * تفعيل الضرائب لجميع المكاتب الجمركية
     */
    public function enableTaxForAll()
    {
        $clearanceOfficeRole = Role::where('name', 'clearance_office')->firstOrFail();

        $offices = User::where('role_id', $clearanceOfficeRole->id)->get();
        $updatedCount = 0;

        foreach ($offices as $office) {
            if (!$office->tax_enabled) {
                $office->update(['tax_enabled' => true]);

                // حفظ سجل التغيير
                OfficeTaxHistory::createNewTaxPeriod(
                    $office->id,
                    true,
                    now(),
                    15.00,
                    'تفعيل الضرائب لجميع المكاتب',
                    auth()->id()
                );
                $updatedCount++;
            }
        }

        return back()->with('success', "تم تفعيل الضرائب لجميع المكاتب الجمركية ({$updatedCount} مكتب).");
    }

    /**
     * إلغاء تفعيل الضرائب لجميع المكاتب الجمركية
     */
    public function disableTaxForAll()
    {
        $clearanceOfficeRole = Role::where('name', 'clearance_office')->firstOrFail();

        $offices = User::where('role_id', $clearanceOfficeRole->id)->get();
        $updatedCount = 0;

        foreach ($offices as $office) {
            if ($office->tax_enabled) {
                $office->update(['tax_enabled' => false]);

                // حفظ سجل التغيير
                OfficeTaxHistory::createNewTaxPeriod(
                    $office->id,
                    false,
                    now(),
                    15.00,
                    'إلغاء تفعيل الضرائب لجميع المكاتب',
                    auth()->id()
                );
                $updatedCount++;
            }
        }

        return back()->with('success', "تم إلغاء تفعيل الضرائب لجميع المكاتب الجمركية ({$updatedCount} مكتب).");
    }

    /**
     * عرض سجل الضرائب لمكتب معين
     */
    public function taxHistory(User $clearance_office)
    {
        // التأكد من أن المستخدم مكتب تخليص جمركي
        if (!$clearance_office->isClearanceOffice()) {
            return back()->with('error', 'المستخدم المحدد ليس مكتب تخليص جمركي.');
        }

        $taxHistory = OfficeTaxHistory::getTaxPeriodsForOffice($clearance_office->id);

        return view('dashboard.clearance_offices.tax_history', compact('clearance_office', 'taxHistory'));
    }
}
