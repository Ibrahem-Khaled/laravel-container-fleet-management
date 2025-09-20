<?php

namespace App\Http\Controllers\system;

use App\Http\Controllers\Controller;
use App\Models\DailyTransaction;
use App\Models\CustodyAccount;
use App\Models\CustodyLedgerEntry;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\User;
use App\Models\Car;
use App\Models\Container;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DailyTransactionController extends Controller
{
    /**
     * دالة مركزية لتحديد كل الموديلات القابلة للربط.
     * تحتوي الآن على 'contexts' لتحديد متى يظهر كل خيار.
     */
    private function getTransactionableModelsConfig()
    {
        // 'income' -> يظهر فقط مع الوارد
        // 'expense' -> يظهر فقط مع المنصرف
        // ['income', 'expense'] -> يظهر في الحالتين
        $config = [
            Car::class => [
                'model' => Car::class,
                'name' => 'مصروفات سيارة',
                'display_column' => 'number',
                'contexts' => ['expense'] // مثال: مصاريف السيارة تعتبر منصرفات
            ],
            // يمكن إضافة موديلات أخرى هنا حسب الحاجة
            Container::class => [
                'model' => Container::class,
                'name' => 'مصاريف الحاوية',
                'display_column' => 'number',
                'contexts' => ['expense'] // مثال: مصاريف الحاوية تعتبر منصرفات
            ],
        ];

        $roles = Role::all();

        foreach ($roles as $role) {
            // مثال: نفترض أن لدينا دور "عميل" ودور "موظف"
            if (stripos($role->name, 'clearance_office') !== false || stripos($role->description, 'مكتب تخليص جمركي') !== false) {
                // الأدوار الاثنين قد تكون للاثنين
                $contexts = ['income'];
                $name = 'مكتب - ' . $role->description;
            } else {
                // الأدوار الأخرى قد تكون للاثنين
                $contexts = ['expense'];
                $name = 'مستخدم - ' . $role->description;
            }

            $config['user_role_' . $role->id] = [
                'model' => User::class,
                'name' => $name,
                'display_column' => 'name',
                'filters' => ['role_id' => $role->id],
                'contexts' => $contexts // السياق الذي يحدد متى يظهر الخيار
            ];
        }

        return $config;
    }

    /**
     * جلب العهد المتاحة حسب نوع الحركة
     */
    private function getAvailableCustodyAccounts($transactionType = null)
    {
        $query = CustodyAccount::with('owner.role')
            ->where('status', 'open')
            ->join('users', 'custody_accounts.user_id', '=', 'users.id')
            ->orderBy('users.name')
            ->select('custody_accounts.*');

        // للمنصرفات: فقط العهد التي لديها رصيد كافي
        if ($transactionType === 'expense') {
            $query->whereRaw('custody_accounts.opening_balance + (
                SELECT COALESCE(SUM(
                    CASE
                        WHEN direction IN ("issue", "income", "transfer_in", "adjustment") THEN amount
                        WHEN direction IN ("return", "expense", "transfer_out") THEN -amount
                        ELSE 0
                    END
                ), 0)
                FROM custody_ledger_entries
                WHERE custody_ledger_entries.custody_account_id = custody_accounts.id
            ) > 0');
        }

        return $query->get();
    }

    /**
     * عرض الصفحة الرئيسية للحركات المالية مع الإحصائيات والبيانات.
     */
    public function index(Request $request)
    {
        $query = DailyTransaction::with(['transactionable', 'custodyAccount.owner'])->latest();

        // فلترة حسب النوع أو الحالة الضريبية
        if ($request->filled('type') && in_array($request->type, ['income', 'expense'])) {
            $query->where('type', $request->type);
        } elseif ($request->filled('filter') && $request->filter == 'taxable') {
            $query->where('tax_value', '>', 0); // فلتر الفواتير الضريبية
        }

        // بحث
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where('notes', 'like', "%{$searchTerm}%");
        }

        $transactions = $query->paginate(15);

        // حساب الإحصائيات
        $stats = [
            'total_income' => DailyTransaction::where('type', 'income')->sum('total_amount'),
            'total_expense' => DailyTransaction::where('type', 'expense')->sum('total_amount'),
            'transactions_count' => DailyTransaction::count(),
        ];
        $stats['net_balance'] = $stats['total_income'] - $stats['total_expense'];

        $transactionable_config = $this->getTransactionableModelsConfig();
        $custody_accounts = $this->getAvailableCustodyAccounts();

        return view('dashboard.transactions.index', compact('transactions', 'stats', 'transactionable_config', 'custody_accounts'));
    }

    /**
     * دالة خاصة بـ Ajax لجلب السجلات ديناميكياً.
     */
    public function getTransactionableRecords(Request $request)
    {
        $request->validate(['key' => 'required|string']);
        $configKey = $request->input('key');

        $allowedConfigs = $this->getTransactionableModelsConfig();

        if (!array_key_exists($configKey, $allowedConfigs)) {
            return response()->json(['error' => 'نوع غير صالح'], 400);
        }

        $config = $allowedConfigs[$configKey];
        $modelClass = $config['model'];
        $displayColumn = $config['display_column'];

        $query = $modelClass::query();

        if (isset($config['filters']) && is_array($config['filters'])) {
            $query->where($config['filters']);
        }

        $records = $query->select('id', $displayColumn)->get()->map(function ($item) use ($displayColumn) {
            return [
                'id' => $item->id,
                'text' => $item->{$displayColumn} . ' (#' . $item->id . ')'
            ];
        });

        return response()->json($records);
    }

    /**
     * تخزين حركة مالية جديدة.
     */
    public function store(Request $request)
    {
        $rules = [
            'type' => 'required|in:income,expense',
            'method' => 'required|in:cash,bank',
            'total_amount' => 'required|numeric|min:0', // نستقبل الإجمالي
            'tax_value' => 'nullable|numeric|min:0|max:100', // الآن هو نسبة الضريبة
            'custody_account_id' => 'nullable|exists:custody_accounts,id',
            'transaction_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'transactionable_key' => 'nullable|string',
            'transactionable_id' => 'nullable|integer|required_with:transactionable_key',
        ];

        $data = $request->validate($rules);

        // التحقق من الرصيد إذا كانت منصرف من عهدة
        if ($data['type'] === 'expense' && $data['custody_account_id']) {
            $custodyAccount = CustodyAccount::find($data['custody_account_id']);
            if ($custodyAccount) {
                $currentBalance = $custodyAccount->currentBalance();
                $totalAmount = $data['total_amount'];

                if ($currentBalance < $totalAmount) {
                    return redirect()->back()
                        ->withErrors(['custody_account_id' => 'الرصيد غير كافي في العهدة المحددة. الرصيد الحالي: ' . number_format($currentBalance, 2) . ' ر.س'])
                        ->withInput();
                }
            }
        }

        $taxPercentage = $data['tax_value'] ?? 0;
        $totalAmount = $data['total_amount'];

        if ($taxPercentage > 0) {
            // الحساب: المبلغ الأساسي = الإجمالي / (1 + (نسبة الضريبة / 100))
            $data['amount'] = $totalAmount / (1 + ($taxPercentage / 100));
        } else {
            $data['amount'] = $totalAmount;
            $data['tax_value'] = 0; // نضمن أنها صفر إذا كانت فارغة
        }

        $configKey = $data['transactionable_key'];
        if ($configKey) {
            $allConfigs = $this->getTransactionableModelsConfig();
            if (isset($allConfigs[$configKey])) {
                $data['transactionable_type'] = $allConfigs[$configKey]['model'];
            }
        } else {
            $data['transactionable_type'] = null;
            $data['transactionable_id'] = null;
        }

        // إنشاء الحركة مع خصم من رصيد العهدة إذا لزم الأمر
        DB::transaction(function () use ($data) {
            $transaction = DailyTransaction::create($data);

            // إذا كانت الحركة مرتبطة بعهدة، أضف سجل في دفتر العهدة
            if ($data['custody_account_id']) {
                $direction = $data['type'] === 'income' ? 'income' : 'expense';

                CustodyLedgerEntry::create([
                    'custody_account_id' => $data['custody_account_id'],
                    'daily_transaction_id' => $transaction->id,
                    'direction' => $direction,
                    'amount' => $data['total_amount'],
                    'currency' => 'SAR', // يمكن تعديلها حسب النظام
                    'occurred_at' => $transaction->created_at, // استخدام تاريخ إنشاء اليومية
                    'created_by' => Auth::id(),
                    'notes' => $data['notes'] ?? 'حركة يومية: ' . ($data['type'] === 'income' ? 'وارد' : 'منصرف'),
                ]);
            }
        });

        return redirect()->route('transactions.index')->with('success', 'تمت إضافة الحركة بنجاح.');
    }

    /**
     * تحديث حركة مالية.
     */
    public function update(Request $request, DailyTransaction $dailyTransaction)
    {
        $rules = [
            'type' => 'required|in:income,expense',
            'method' => 'required|in:cash,bank',
            'total_amount' => 'required|numeric|min:0',
            'tax_value' => 'nullable|numeric|min:0|max:100', // نسبة الضريبة
            'custody_account_id' => 'nullable|exists:custody_accounts,id',
            'transaction_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'transactionable_key' => 'nullable|string',
            'transactionable_id' => 'nullable|integer|required_with:transactionable_key',
        ];

        $data = $request->validate($rules);

        // التحقق من الرصيد إذا كانت منصرف من عهدة
        if ($data['type'] === 'expense' && $data['custody_account_id']) {
            $custodyAccount = CustodyAccount::find($data['custody_account_id']);
            if ($custodyAccount) {
                $currentBalance = $custodyAccount->currentBalance();
                $totalAmount = $data['total_amount'];

                // إضافة الرصيد الحالي للحركة إذا كانت موجودة مسبقاً
                if ($dailyTransaction->custody_account_id == $data['custody_account_id'] && $dailyTransaction->type === 'expense') {
                    $currentBalance += $dailyTransaction->total_amount;
                }

                if ($currentBalance < $totalAmount) {
                    return redirect()->back()
                        ->withErrors(['custody_account_id' => 'الرصيد غير كافي في العهدة المحددة. الرصيد الحالي: ' . number_format($currentBalance, 2) . ' ر.س'])
                        ->withInput();
                }
            }
        }

        $taxPercentage = $data['tax_value'] ?? 0;
        $totalAmount = $data['total_amount'];

        if ($taxPercentage > 0) {
            $data['amount'] = $totalAmount / (1 + ($taxPercentage / 100));
        } else {
            $data['amount'] = $totalAmount;
            $data['tax_value'] = 0;
        }

        $configKey = $data['transactionable_key'];
        if ($configKey) {
            $allConfigs = $this->getTransactionableModelsConfig();
            if (isset($allConfigs[$configKey])) {
                $data['transactionable_type'] = $allConfigs[$configKey]['model'];
            }
        } else {
            $data['transactionable_type'] = null;
            $data['transactionable_id'] = null;
        }

        // تحديث الحركة مع تحديث سجل العهدة إذا لزم الأمر
        DB::transaction(function () use ($data, $dailyTransaction) {
            $dailyTransaction->update($data);

            // إذا كانت الحركة مرتبطة بعهدة، حدث أو أنشئ سجل في دفتر العهدة
            if ($data['custody_account_id']) {
                $direction = $data['type'] === 'income' ? 'income' : 'expense';

                // البحث عن سجل موجود أو إنشاء جديد
                $ledgerEntry = CustodyLedgerEntry::where('daily_transaction_id', $dailyTransaction->id)->first();

                if ($ledgerEntry) {
                    // تحديث السجل الموجود
                    $ledgerEntry->update([
                        'custody_account_id' => $data['custody_account_id'],
                        'direction' => $direction,
                        'amount' => $data['total_amount'],
                        'occurred_at' => $dailyTransaction->created_at, // استخدام تاريخ إنشاء اليومية
                        'notes' => $data['notes'] ?? 'حركة يومية: ' . ($data['type'] === 'income' ? 'وارد' : 'منصرف'),
                    ]);
                } else {
                    // إنشاء سجل جديد
                    CustodyLedgerEntry::create([
                        'custody_account_id' => $data['custody_account_id'],
                        'daily_transaction_id' => $dailyTransaction->id,
                        'direction' => $direction,
                        'amount' => $data['total_amount'],
                        'currency' => 'SAR',
                        'occurred_at' => now(),
                        'created_by' => Auth::id(),
                        'notes' => $data['notes'] ?? 'حركة يومية: ' . ($data['type'] === 'income' ? 'وارد' : 'منصرف'),
                    ]);
                }
            } else {
                // إذا لم تعد الحركة مرتبطة بعهدة، احذف السجل الموجود
                CustodyLedgerEntry::where('daily_transaction_id', $dailyTransaction->id)->delete();
            }
        });

        return redirect()->route('transactions.index')->with('success', 'تم تحديث الحركة بنجاح.');
    }

    /**
     * حذف حركة مالية (حذف ناعم).
     */
    public function destroy(DailyTransaction $dailyTransaction)
    {
        DB::transaction(function () use ($dailyTransaction) {
            // حذف سجل العهدة المرتبط إذا كان موجوداً
            CustodyLedgerEntry::where('daily_transaction_id', $dailyTransaction->id)->delete();

            // حذف الحركة
            $dailyTransaction->delete();
        });

        return redirect()->route('transactions.index')->with('success', 'تم حذف الحركة بنجاح.');
    }
}
