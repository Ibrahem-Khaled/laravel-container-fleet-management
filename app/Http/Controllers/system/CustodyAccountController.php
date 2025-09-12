<?php

namespace App\Http\Controllers\system;

use App\Http\Controllers\Controller;
use App\Models\{CustodyAccount, User};
use App\Http\Requests\dashboard\{StoreCustodyAccountRequest, UpdateCustodyAccountRequest};

use Illuminate\Http\Request;

class CustodyAccountController extends Controller
{
    public function index()
    {
        // $this->authorize('viewAny', CustodyAccount::class);
        $accounts = CustodyAccount::with(['user'])->latest()->paginate(12);
        return view('dashboard.custody.accounts.index', compact('accounts'));
    }

    public function create()
    {
        // $this->authorize('create', CustodyAccount::class);
        $users = User::orderBy('name')->get(['id', 'name']);
        return view('dashboard.custody.accounts.create', compact('users'));
    }

    public function store(StoreCustodyAccountRequest $request)
    {
        $acc = CustodyAccount::create([
            'user_id' => $request->user_id,
            'opening_balance' => $request->opening_balance ?? 0,
            'opened_by' => $request->user()->id,
            'opened_at' => now(),
            'notes' => $request->notes,
        ]);
        return redirect()->route('custody.accounts.show', $acc)->with('ok', 'تم إنشاء حساب العهدة');
    }

    public function show(CustodyAccount $custody_account)
    {
        // $this->authorize('view', $custody_account);
        $entries = $custody_account->ledgerEntries()->latest('occurred_at')->paginate(15);
        $balance = $custody_account->currentBalance();
        return view('dashboard.custody.accounts.show', compact('custody_account', 'entries', 'balance'));
    }

    public function edit(CustodyAccount $custody_account)
    {
        // $this->authorize('update', $custody_account);
        return view('dashboard.custody.accounts.edit', compact('custody_account'));
    }

    public function update(UpdateCustodyAccountRequest $request, CustodyAccount $custody_account)
    {
        $custody_account->update($request->validated());
        return back()->with('ok', 'تم تحديث الحساب');
    }

    public function destroy(CustodyAccount $custody_account)
    {
        // $this->authorize('delete', $custody_account);
        $custody_account->delete();
        return redirect()->route('custody.accounts.index')->with('ok', 'تم حذف الحساب');
    }
}
