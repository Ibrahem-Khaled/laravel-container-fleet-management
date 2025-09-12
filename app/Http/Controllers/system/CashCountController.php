<?php

namespace App\Http\Controllers\system;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{CustodyAccount, CashCount};
use App\Http\Requests\dashboard\StoreCashCountRequest;
use App\Services\CustodyService;


class CashCountController extends Controller
{
    public function __construct(private CustodyService $service) {}

    public function create(CustodyAccount $custody_account)
    {
        // $this->authorize('create', [CashCount::class, $custody_account]);
        $expected = $custody_account->currentBalance();
        return view('dashboard.custody.counts.create', compact('custody_account', 'expected'));
    }

    public function store(StoreCashCountRequest $request, CustodyAccount $custody_account)
    {
        $expected = $request->total_expected; // مرسل من الواجهة (محسوب مسبقًا)
        $counted  = $request->total_counted;
        $this->service->countAndPost($custody_account, $expected, $counted, $request->user()->id, $request->notes);
        return redirect()->route('custody.accounts.show', $custody_account)->with('ok', 'تم اعتماد الجرد وترحيل التسوية');
    }
}
