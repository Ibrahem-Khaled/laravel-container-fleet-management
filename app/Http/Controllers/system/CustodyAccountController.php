<?php

declare(strict_types=1);

namespace App\Http\Controllers\system;

use App\Http\Controllers\Controller;
use App\Http\Requests\dashboard\StoreCustodyIssueRequest;
use App\Models\{CustodyAccount, Role};
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

final class CustodyAccountController extends Controller
{
    public function index(Request $req): View
    {
        $search = (string) $req->string('search');
        $roleId = $req->integer('role_id') ?: null;
        $status = (string) ($req->string('status')->toString() ?: 'open');

        $accounts = CustodyAccount::query()
            ->with(['owner.role:id,name', 'dailyTransactions'])               // eager minimal
            ->when($search, fn($q) => $q->search($search))
            ->when($roleId, fn($q) => $q->forRole($roleId))
            ->when($status !== 'all', fn($q) => $q->status($status))
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        $openCount   = CustodyAccount::status('open')->count();
        $closedCount = CustodyAccount::status('closed')->count();

        // إحصائيات إضافية للعهد
        $totalOpeningBalance = CustodyAccount::sum('opening_balance');
        $totalCurrentBalance = CustodyAccount::get()->sum(function($account) {
            return $account->currentBalance();
        });
        $totalTransactions = CustodyAccount::withCount('dailyTransactions')->get()->sum('daily_transactions_count');

        $roles = Role::orderBy('name')->get(['id', 'name', 'description']);

        return view('dashboard.custody.index', compact(
            'accounts',
            'openCount',
            'closedCount',
            'totalOpeningBalance',
            'totalCurrentBalance',
            'totalTransactions',
            'roles',
            'roleId',
            'status',
            'search'
        ));
    }

    public function store(Request $req): RedirectResponse
    {
        $data = $req->validate([
            'user_id'         => ['required', 'exists:users,id'],
            'opening_balance' => ['nullable', 'numeric'],
            'notes'           => ['nullable', 'string', 'max:1000'],
        ]); // قواعد التحقق الرسمية. :contentReference[oaicite:1]{index=1}

        $data['status']    = 'open';
        $data['opened_by'] = Auth::id();
        $data['opened_at'] = now();

        CustodyAccount::create($data);

        return back()->with('success', 'تم إنشاء عهدة جديدة.');
    }

    public function show(CustodyAccount $custody_account, Request $req): View
    {
        $account = $custody_account->load('owner');

        $daily = $account->dailyTransactions()
            ->when($req->filled('type'), fn($q) => $q->where('type', $req->string('type')))
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $sumIncome  = $account->dailyTransactions()->where('type', 'income')->sum('total_amount');
        $sumExpense = $account->dailyTransactions()->where('type', 'expense')->sum('total_amount');

        return view('dashboard.custody.show', compact('account', 'daily', 'sumIncome', 'sumExpense'));
    }

    public function update(Request $req, CustodyAccount $custody_account): RedirectResponse
    {
        $data = $req->validate([
            'opening_balance' => ['nullable', 'numeric'],
            'status'          => ['required', 'in:open,closed'],
            'notes'           => ['nullable', 'string', 'max:1000'],
        ]);

        if ($data['status'] === 'closed' && $custody_account->status !== 'closed') {
            $data['closed_by'] = Auth::id();
            $data['closed_at'] = now();
        }

        $custody_account->update($data);

        return back()->with('success', 'تم تحديث بيانات العهدة.');
    }

    public function destroy(CustodyAccount $custody_account): RedirectResponse
    {
        $custody_account->delete();

        return back()->with('success', 'تم حذف العهدة.');
    }

    /**
     * زيادة العهدة فقط (Issue) — من شاشة العهدة.
     * يمنع أي عمليات خصم هنا. الخصم يتم من صفحة اليومية.
     */
    public function storeIssue(StoreCustodyIssueRequest $request, CustodyAccount $account): \Illuminate\Http\RedirectResponse
    {
        if ($account->status !== 'open') {
            return back()->with('error', 'لا يمكن إضافة حركة على عهدة مغلقة.')->withInput();
        }

        DB::transaction(function () use ($request, $account) {
            $amount   = (float) $request->validated('amount');
            $currency = $request->validated('currency');
            $occurred = $request->validated('occurred_at') ?? now();
            $method   = $request->validated('method') ?? 'cash';
            $notes    = $request->validated('notes');

            // 1) أنشئ سطر يومية (وارد) حتى يظهر في جدول اليومية
            $daily = $account->dailyTransactions()->create([
                'type'         => 'income',      // وارد على العهدة
                'transactionable_type' => \App\Models\CustodyAccount::class,
                'transactionable_id'   => $account->id,
                'method'       => $method,       // نقدي/بنك
                'amount'       => $amount,       // المبلغ الأساسي
                'tax_value'    => 0,             // عدّل حسب نظامك
                'total_amount' => $amount,       // = amount + tax_value
                'notes'        => $notes,
            ]);

            // 2) أنشئ سطر الدفتر واربطه بسطر اليومية
            $account->entries()->create([
                'direction'            => 'issue', // زيادة عهدة
                'amount'               => $amount,
                'currency'             => $currency,
                'occurred_at'          => $occurred,
                'reference_id'         => $daily->id,
                'reference_type'       => \App\Models\DailyTransaction::class,
                'counterparty_user_id' => null,
                'created_by'           => Auth::id(),
                'notes'                => $notes,
            ]);
        }); // تُدار الذرّية آليًا: Commit/Rollback. :contentReference[oaicite:2]{index=2}

        return back()->with('success', 'تم زيادة العهدة للمستخدم وظهرت في اليومية.');
    }
}
