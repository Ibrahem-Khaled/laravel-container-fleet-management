<?php

namespace App\Http\Controllers\system;

use App\Http\Controllers\Controller;
use App\Models\DailyTransaction;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\User;
use App\Models\Car;

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
     * عرض الصفحة الرئيسية للحركات المالية مع الإحصائيات والبيانات.
     */
    public function index(Request $request)
    {
        $query = DailyTransaction::with('transactionable')->latest();

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

        return view('dashboard.transactions.index', compact('transactions', 'stats', 'transactionable_config'));
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
            'notes' => 'nullable|string',
            'transactionable_key' => 'nullable|string',
            'transactionable_id' => 'nullable|integer|required_with:transactionable_key',
        ];

        $data = $request->validate($rules);

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

        DailyTransaction::create($data);

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
            'notes' => 'nullable|string',
            'transactionable_key' => 'nullable|string',
            'transactionable_id' => 'nullable|integer|required_with:transactionable_key',
        ];

        $data = $request->validate($rules);

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

        $dailyTransaction->update($data);

        return redirect()->route('transactions.index')->with('success', 'تم تحديث الحركة بنجاح.');
    }

    /**
     * حذف حركة مالية (حذف ناعم).
     */
    public function destroy(DailyTransaction $dailyTransaction)
    {
        $dailyTransaction->delete();
        return redirect()->route('transactions.index')->with('success', 'تم حذف الحركة بنجاح.');
    }
}
