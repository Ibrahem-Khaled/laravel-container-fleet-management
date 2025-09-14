<?php

namespace App\Http\Controllers\system;

use App\Http\Controllers\Controller;
use App\Models\{CustodyAccount, Role, User};
use App\Http\Requests\dashboard\{StoreCustodyAccountRequest, UpdateCustodyAccountRequest};

use Illuminate\Http\Request;

class CustodyAccountController extends Controller
{
    public function index(Request $req)
    {
        $search  = $req->string('search')->toString();
        $roleId  = $req->integer('role_id') ?: null;   // <-- بدل role
        $status  = $req->string('status')->toString() ?: 'open';

        $accounts = CustodyAccount::query()
            ->with(['owner.role']) // <-- هنحتاج اسم الدور في الواجهة
            ->when($search, fn($q) => $q->where(function ($qq) use ($search) {
                $qq->whereHas('owner', fn($uq) => $uq
                    ->where('name',  'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('phone', 'like', "%$search%"));
            }))
            ->when($roleId, fn($q) => $q->whereHas('owner', fn($uq) => $uq->where('role_id', $roleId))) // <-- هنا التغيير
            ->when($status !== 'all', fn($q) => $q->where('status', $status))
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        $openCount   = CustodyAccount::where('status', 'open')->count();
        $closedCount = CustodyAccount::where('status', 'closed')->count();

        // نجيب كل الأدوار من جدول roles لعرض تبويب أدوار
        $roles = Role::orderBy('name')->get(['id', 'name', 'description']);

        return view('dashboard.custody.index', compact(
            'accounts',
            'openCount',
            'closedCount',
            'roles',
            'roleId',
            'status',
            'search'
        ));
    }

    public function store(Request $req)
    {
        $data = $req->validate([
            'user_id' => ['required', 'exists:users,id'],
            'opening_balance' => ['nullable', 'numeric'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]); // توثيق قواعد التحقق في Laravel. :contentReference[oaicite:3]{index=3}

        $data['status'] = 'open';
        $data['opened_by'] = auth()->id();
        $data['opened_at'] = now();

        CustodyAccount::create($data);
        return back()->with('success', 'تم إنشاء عهدة جديدة.');
    }

    public function show(CustodyAccount $custody_account, Request $req)
    {
        $account = $custody_account->load('owner');
        $daily = $account->dailyTransactions()
            ->when($req->filled('type'), fn($q) => $q->where('type', $req->type))
            ->orderByDesc('id')->paginate(20)->withQueryString();

        // مجموعات سريعة
        $sumIncome  = $account->dailyTransactions()->where('type', 'income')->sum('total_amount');
        $sumExpense = $account->dailyTransactions()->where('type', 'expense')->sum('total_amount');

        return view('dashboard.custody.show', compact('account', 'daily', 'sumIncome', 'sumExpense'));
    }

    public function update(Request $req, CustodyAccount $custody_account)
    {
        $data = $req->validate([
            'opening_balance' => ['nullable', 'numeric'],
            'status' => ['required', 'in:open,closed'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($data['status'] === 'closed' && $custody_account->status !== 'closed') {
            $data['closed_by'] = auth()->id();
            $data['closed_at'] = now();
        }

        $custody_account->update($data);
        return back()->with('success', 'تم تحديث بيانات العهدة.');
    }

    public function destroy(CustodyAccount $custody_account)
    {
        $custody_account->delete();
        return back()->with('success', 'تم حذف العهدة.');
    }
}
