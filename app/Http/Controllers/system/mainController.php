<?php

namespace App\Http\Controllers\system;

use App\Http\Controllers\Controller;
use App\Models\Container;
use App\Models\DailyTransaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class mainController extends Controller
{
    public function index()
    {
        $start  = request('start_date');
        $end    = request('end_date');
        $status = request('status');
        $term   = request('q');

        // === ملخص عام (كما كان) ===
        $summary = DailyTransaction::query()
            ->withinDateRange($start, $end)
            ->selectRaw("
                SUM(CASE WHEN type = 'income'  THEN total_amount ELSE 0 END) as total_income,
                SUM(CASE WHEN type = 'expense' THEN total_amount ELSE 0 END) as total_expense,
                SUM(tax_value) as total_tax
            ")
            ->first();

        $totalIncome  = (float) ($summary->total_income  ?? 0);
        $totalExpense = (float) ($summary->total_expense ?? 0);
        $totalTax     = (float) ($summary->total_tax     ?? 0);
        $balance      = $totalIncome - $totalExpense;

        // === الاتجاه الزمني للمعاملات (Line) ===
        $txTrend = DailyTransaction::query()
            ->withinDateRange($start, $end)
            ->selectRaw("
                DATE(created_at) as d,
                SUM(CASE WHEN type='income'  THEN total_amount ELSE 0 END) as income_sum,
                SUM(CASE WHEN type='expense' THEN total_amount ELSE 0 END) as expense_sum
            ")
            ->groupBy('d')->orderBy('d')->get();

        // === قاعدة الحاويات (فلاتر) ===
        $containersBase = Container::query()
            ->status($status)
            ->search($term)
            ->withinTransferDate($start, $end);

        // عدد الحاويات لكل حالة
        $byStatus = (clone $containersBase)
            ->select('status', DB::raw('COUNT(*) as cnt'))
            ->groupBy('status')->get()->pluck('cnt', 'status');

        // إجمالي تكلفة التحويلات
        $transferSum = (clone $containersBase)
            ->withSum('transferOrders', 'price')
            ->get()
            ->sum('transfer_orders_sum_price');

        // توزيع طرق الدفع (Pie)
        $byMethod = DailyTransaction::query()
            ->withinDateRange($start, $end)
            ->select('method', DB::raw('SUM(total_amount) as total'))
            ->groupBy('method')->get();

        // تايملاين حالات الحاويات (Stacked)
        $containerTimeline = (clone $containersBase)
            ->selectRaw("DATE(transfer_date) as d, status, COUNT(*) as c")
            ->groupBy('d', 'status')->orderBy('d')->get();

        // === إحصائيات تفصيلية للوارد/المنصرف حسب الطريقة ===
        $totals = DailyTransaction::query()
            ->withinDateRange($start, $end)
            ->selectRaw("
                SUM(CASE WHEN type='income'  AND method='bank' THEN total_amount ELSE 0 END) as income_bank,
                SUM(CASE WHEN type='income'  AND method='cash' THEN total_amount ELSE 0 END) as income_cash,
                SUM(CASE WHEN type='expense' AND method='bank' THEN total_amount ELSE 0 END) as expense_bank,
                SUM(CASE WHEN type='expense' AND method='cash' THEN total_amount ELSE 0 END) as expense_cash
            ")
            ->first();

        $income_bank   = (float) ($totals->income_bank   ?? 0);
        $income_cash   = (float) ($totals->income_cash   ?? 0);
        $expense_bank  = (float) ($totals->expense_bank  ?? 0);
        $expense_cash  = (float) ($totals->expense_cash  ?? 0);


        $officesStats = User::query()
            ->withRoles('clearance_office') // سكوبك: whereHas('role' ...) على اسم الدور
            ->leftJoin('customs_declarations as cd', 'cd.clearance_office_id', '=', 'users.id')
            ->leftJoin('containers as c', 'c.customs_id', '=', 'cd.id')
            ->when($status, fn($q) => $q->where('c.status', $status))                       // فلتر الحالة
            ->when($start,  fn($q) => $q->whereDate('c.transfer_date', '>=', $start))       // فلتر التاريخ من
            ->when($end,    fn($q) => $q->whereDate('c.transfer_date', '<=', $end))         // فلتر التاريخ إلى
            ->groupBy('users.id', 'users.name')
            ->orderByDesc(DB::raw("SUM(CASE WHEN (c.price IS NULL OR c.price = 0) THEN 1 ELSE 0 END)"))
            ->get([
                'users.id',
                'users.name',
                DB::raw("COUNT(c.id) as containers_total"),
                DB::raw("SUM(CASE WHEN (c.price IS NULL OR c.price = 0) THEN 1 ELSE 0 END) as containers_unpriced"),
            ]);

        $stats = [
            'clients_count' => User::count(),
            'income_bank'   => $income_bank,
            'income_cash'   => $income_cash,
            'expense_bank'  => $expense_bank,
            'expense_cash'  => $expense_cash,
            'income_diff'   => $income_bank - $income_cash,     // فرق وارد بنك - كاش
            'expense_diff'  => $expense_bank - $expense_cash,   // فرق منصرف بنك - كاش
            'net_bank'      => $income_bank  - $expense_bank,   // صافي البنك
            'net_cash'      => $income_cash  - $expense_cash,   // صافي الكاش
            'net_total'     => ($income_bank + $income_cash) - ($expense_bank + $expense_cash),
        ];

        return view('dashboard.index', [
            'filters'   => compact('start', 'end', 'status', 'term'),
            'kpis'      => [
                'income'  => $totalIncome,
                'expense' => $totalExpense,
                'tax'     => $totalTax,
                'balance' => $balance,
            ],
            'stats'     => $stats,
            'txTrend'   => $txTrend,
            'byStatus'  => $byStatus,
            'byMethod'  => $byMethod,
            'transferSum' => (float) $transferSum,
            'containerTimeline' => $containerTimeline,
            'officesStats' => $officesStats,
        ]);
    }
}
