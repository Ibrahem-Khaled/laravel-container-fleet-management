<?php

namespace App\Http\Controllers\system;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{CustodyAccount, CustodyLedgerEntry};
use App\Http\Requests\dashboard\StoreLedgerEntryRequest;
use App\Services\CustodyService;

class CustodyLedgerEntryController extends Controller
{
    public function __construct(private CustodyService $service) {}

    public function create(CustodyAccount $custody_account)
    {
        // $this->authorize('createEntry', $custody_account);
        return view('dashboard.custody.entries.create', compact('custody_account'));
    }

    public function store(StoreLedgerEntryRequest $request, CustodyAccount $custody_account)
    {
        $entry = $this->service->entry($custody_account, $request->validated(), $request->user()->id);
        return redirect()->route('custody.accounts.show', $custody_account)->with('ok', 'تم تسجيل الحركة #' . $entry->id);
    }

    public function destroy(CustodyAccount $custody_account, CustodyLedgerEntry $entry)
    {
        // $this->authorize('delete', $entry);
        $entry->delete();
        return back()->with('ok', 'تم حذف الحركة');
    }
}
