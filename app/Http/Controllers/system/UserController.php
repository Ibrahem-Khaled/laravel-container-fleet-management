<?php

namespace App\Http\Controllers\system;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // --- 1. تحديد الأدوار التي سيتم استبعادها ---
        $excludedRoles = ['client', 'clearance_office'];

        // --- 2. جلب الأدوار المطلوبة فقط لعرضها في التبويبات ---
        // الصيغة الصحيحة هي whereNotIn
        $roles = Role::whereNotIn('name', $excludedRoles)->get();

        // --- 3. جلب IDs الأدوار المستبعدة لاستخدامها في فلترة المستخدمين ---
        $excludedRoleIds = Role::whereIn('name', $excludedRoles)->pluck('id');

        // --- 4. بدء استعلام المستخدمين مع تطبيق الفلترة الأساسية (استبعاد الأدوار غير المرغوب فيها) ---
        $query = User::with('role')->whereNotIn('role_id', $excludedRoleIds)->latest();

        // فلترة حسب الدور المختار من التبويب (يبقى كما هو)
        if ($request->filled('role')) {
            $roleId = $request->role;
            $query->where('role_id', $roleId);
        }

        // فلترة حسب البحث (يبقى كما هو)
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('email', 'like', "%{$searchTerm}%")
                    ->orWhere('phone', 'like', "%{$searchTerm}%");
            });
        }

        $users = $query->paginate(10)->withQueryString();

        // --- 5. حساب الإحصائيات بعد تطبيق الفلترة الأساسية ---
        $stats = [
            'totalUsers' => User::whereNotIn('role_id', $excludedRoleIds)->count(),
            'activeUsers' => User::whereNotIn('role_id', $excludedRoleIds)->where('is_active', true)->count(),
            'rolesCount' => $roles->count(), // هذا صحيح لأنه يعتمد على الأدوار التي جلبناها في البداية
            'roleCounts' => User::whereNotIn('role_id', $excludedRoleIds)
                ->selectRaw('role_id, count(*) as count')
                ->groupBy('role_id')
                ->pluck('count', 'role_id'),
        ];

        return view('dashboard.users.index', compact('users', 'roles', 'stats'));
    }
    /**
     * تخزين مستخدم جديد.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20|unique:users',
            'role_id' => 'required|exists:roles,id',
            'password' => 'required|confirmed|min:6',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'is_active' => 'required|boolean',
            'salary' => 'nullable|numeric|min:0',
        ]);

        $data = $request->except('password', 'avatar');
        $data['password'] = Hash::make($request->password);

        // تعيين قيمة افتراضية للراتب إذا كان فارغاً
        $data['salary'] = $request->salary ?? 0;

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        User::create($data);

        return redirect()->route('users.index')->with('success', 'تمت إضافة المستخدم بنجاح.');
    }

    /**
     * تحديث بيانات مستخدم.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:20|unique:users,phone,' . $user->id,
            'role_id' => 'required|exists:roles,id',
            'password' => 'nullable|confirmed|min:6',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'is_active' => 'required|boolean',
            'salary' => 'nullable|numeric|min:0',
        ]);

        $data = $request->except('password', 'avatar');

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('avatar')) {
            // يمكنك إضافة كود لحذف الصورة القديمة إذا أردت
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'تم تحديث بيانات المستخدم بنجاح.');
    }

    /**
     * حذف مستخدم (حذف ناعم).
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'تم حذف المستخدم بنجاح.');
    }
}
