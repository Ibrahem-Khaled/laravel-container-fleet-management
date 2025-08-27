<?php

namespace App\Http\Controllers\system;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
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
            'operational_number' => 'nullable|integer|unique:users,operational_number',
        ]);

        $clearanceOfficeRole = Role::where('name', 'clearance_office')->firstOrFail();
        $validated['role_id'] = $clearanceOfficeRole->id;

        // لا نطلب كلمة مرور، يمكن تركها فارغة
        // $validated['password'] = Hash::make(Str::random(10));

        if ($request->hasFile('avatar')) {
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        User::create($validated);

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
            'operational_number' => ['nullable', 'integer', Rule::unique('users')->ignore($clearance_office->id)],
        ]);

        if ($request->hasFile('avatar')) {
            // Optional: Delete old avatar
            // Storage::disk('public')->delete($clearance_office->avatar);
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $clearance_office->update($validated);

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
}
