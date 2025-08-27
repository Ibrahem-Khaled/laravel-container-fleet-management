<?php

namespace App\Http\Controllers\system;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // الاستعلام الأساسي للأدوار مع حساب عدد المستخدمين المرتبطين بكل دور
        $query = Role::withCount('users');

        // فلترة البحث
        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        }

        // فلترة حسب التبويب (الدور المحدد)
        $selectedRole = $request->input('role');
        if ($selectedRole && $selectedRole !== 'all') {
            $query->where('name', $selectedRole);
        }

        $roles = $query->paginate(10);

        // الإحصائيات
        $stats = [
            'total_roles' => Role::count(),
            'admins_count' => User::whereHas('role', fn($q) => $q->where('name', 'admin'))->count(),
            'clients_count' => User::whereHas('role', fn($q) => $q->where('name', 'client'))->count(),
        ];

        // جلب كل الأدوار لعرضها في التبويبات
        $allRoles = Role::pluck('name');

        return view('dashboard.roles.index', compact('roles', 'stats', 'allRoles', 'selectedRole'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'description' => 'nullable|string|max:255',
        ]);

        Role::create($validated);

        return redirect()->route('roles.index')->with('success', 'تمت إضافة الدور بنجاح.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles')->ignore($role->id)],
            'description' => 'nullable|string|max:255',
        ]);

        $role->update($validated);

        return redirect()->route('roles.index')->with('success', 'تم تعديل الدور بنجاح.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        // منع حذف الأدوار الأساسية إذا أردت
        if (in_array($role->name, ['admin', 'client'])) {
            return redirect()->route('roles.index')->with('error', 'لا يمكن حذف هذا الدور الأساسي.');
        }

        // تأكد من عدم وجود مستخدمين مرتبطين بالدور قبل الحذف
        if ($role->users()->count() > 0) {
            return redirect()->route('roles.index')->with('error', 'لا يمكن حذف الدور لوجود مستخدمين مرتبطين به.');
        }

        $role->delete();

        return redirect()->route('roles.index')->with('success', 'تم حذف الدور بنجاح.');
    }
}
