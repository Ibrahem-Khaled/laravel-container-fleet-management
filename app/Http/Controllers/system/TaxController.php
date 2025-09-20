<?php

namespace App\Http\Controllers\system;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\DailyTransaction;
use App\Models\OfficeTaxHistory;
use App\Services\TaxCalculationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaxController extends Controller
{
    protected $taxService;

    public function __construct(TaxCalculationService $taxService)
    {
        $this->taxService = $taxService;
    }

    /**
     * عرض صفحة الضرائب الرئيسية
     */
    public function index(Request $request)
    {
        $year = $request->get('year', now()->year);
        $quarter = $request->get('quarter', $this->getCurrentQuarter());

        // تحديد تواريخ الربع
        $quarterDates = $this->getQuarterDates($year, $quarter);

        // جلب المكاتب الجمركية المفعلة الضرائب
        $clearanceOffices = $this->getActiveTaxOffices($quarterDates['start'], $quarterDates['end']);

        // حساب الضرائب المحصلة لكل مكتب
        $collectedTaxes = $this->calculateCollectedTaxes($clearanceOffices, $quarterDates['start'], $quarterDates['end']);

        // حساب الضرائب المدفوعة من اليومية
        $paidTaxes = $this->calculatePaidTaxes($quarterDates['start'], $quarterDates['end']);

        // إحصائيات عامة
        $stats = $this->calculateTaxStats($collectedTaxes, $paidTaxes);

        return view('dashboard.taxes.index', compact(
            'year',
            'quarter',
            'quarterDates',
            'clearanceOffices',
            'collectedTaxes',
            'paidTaxes',
            'stats'
        ));
    }

    /**
     * الحصول على الربع الحالي
     */
    private function getCurrentQuarter()
    {
        $month = now()->month;
        if ($month <= 3) return 1;
        if ($month <= 6) return 2;
        if ($month <= 9) return 3;
        return 4;
    }

    /**
     * تحديد تواريخ الربع
     */
    private function getQuarterDates($year, $quarter)
    {
        switch ($quarter) {
            case 1:
                return [
                    'start' => Carbon::create($year, 1, 1)->startOfDay(),
                    'end' => Carbon::create($year, 3, 31)->endOfDay(),
                    'name' => 'الربع الأول'
                ];
            case 2:
                return [
                    'start' => Carbon::create($year, 4, 1)->startOfDay(),
                    'end' => Carbon::create($year, 6, 30)->endOfDay(),
                    'name' => 'الربع الثاني'
                ];
            case 3:
                return [
                    'start' => Carbon::create($year, 7, 1)->startOfDay(),
                    'end' => Carbon::create($year, 9, 30)->endOfDay(),
                    'name' => 'الربع الثالث'
                ];
            case 4:
                return [
                    'start' => Carbon::create($year, 10, 1)->startOfDay(),
                    'end' => Carbon::create($year, 12, 31)->endOfDay(),
                    'name' => 'الربع الرابع'
                ];
            default:
                return $this->getQuarterDates($year, 1);
        }
    }

    /**
     * جلب المكاتب الجمركية المفعلة الضرائب في الفترة المحددة
     */
    private function getActiveTaxOffices($startDate, $endDate)
    {
        return User::whereHas('role', function ($query) {
            $query->where('name', 'clearance_office');
        })
        ->where(function ($query) use ($startDate, $endDate) {
            // المكاتب التي كانت مفعلة الضرائب في أي وقت خلال الفترة
            $query->whereHas('taxHistory', function ($taxQuery) use ($startDate, $endDate) {
                $taxQuery->where('tax_enabled', true)
                    ->where(function ($periodQuery) use ($startDate, $endDate) {
                        $periodQuery->whereBetween('effective_from', [$startDate, $endDate])
                            ->orWhere(function ($subQuery) use ($startDate, $endDate) {
                                $subQuery->where('effective_from', '<=', $startDate)
                                    ->where(function ($endQuery) use ($endDate) {
                                        $endQuery->whereNull('effective_to')
                                            ->orWhere('effective_to', '>=', $endDate);
                                    });
                            });
                    });
            })
            // أو المكاتب التي لديها tax_enabled = true حالياً
            ->orWhere('tax_enabled', true);
        })
        ->with(['taxHistory' => function ($query) use ($startDate, $endDate) {
            $query->where('tax_enabled', true)
                ->where(function ($periodQuery) use ($startDate, $endDate) {
                    $periodQuery->whereBetween('effective_from', [$startDate, $endDate])
                        ->orWhere(function ($subQuery) use ($startDate, $endDate) {
                            $subQuery->where('effective_from', '<=', $startDate)
                                ->where(function ($endQuery) use ($endDate) {
                                    $endQuery->whereNull('effective_to')
                                        ->orWhere('effective_to', '>=', $endDate);
                                });
                        });
                });
        }])
        ->get();
    }

    /**
     * حساب الضرائب المحصلة لكل مكتب مع التفاصيل الشهرية
     */
    private function calculateCollectedTaxes($offices, $startDate, $endDate)
    {
        $collectedTaxes = [];

        foreach ($offices as $office) {
            // جلب إجمالي الواردات للمكتب في الفترة
            $totalRevenue = $this->getOfficeRevenue($office->id, $startDate, $endDate);

            // حساب الضريبة المستحقة
            $taxCalculation = $this->taxService->calculateTaxForOffice($office->id, $totalRevenue);

            // جلب التفاصيل الشهرية للمكتب
            $monthlyDetails = $this->getOfficeMonthlyDetails($office->id, $startDate, $endDate);

            $collectedTaxes[] = [
                'office' => $office,
                'total_revenue' => $totalRevenue,
                'tax_calculation' => $taxCalculation,
                'tax_rate' => $taxCalculation['tax_rate'],
                'tax_amount' => $taxCalculation['tax_amount'],
                'tax_enabled' => $taxCalculation['tax_enabled'],
                'monthly_details' => $monthlyDetails
            ];
        }

        return $collectedTaxes;
    }

    /**
     * جلب إجمالي الواردات لمكتب في فترة معينة
     */
    private function getOfficeRevenue($officeId, $startDate, $endDate)
    {
        // جلب إجمالي أسعار الحاويات للمكتب في الفترة
        $containerRevenue = DB::table('customs_declarations as cd')
            ->join('containers as c', 'c.customs_id', '=', 'cd.id')
            ->where('cd.clearance_office_id', $officeId)
            ->whereBetween('c.transfer_date', [$startDate, $endDate])
            ->whereNotNull('c.price')
            ->where('c.price', '>', 0)
            ->sum('c.price');

        // جلب إجمالي المعاملات المالية للمكتب في الفترة
        $transactionRevenue = DailyTransaction::where('transactionable_type', User::class)
            ->where('transactionable_id', $officeId)
            ->where('type', 'income')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_amount');

        return (float) $containerRevenue + (float) $transactionRevenue;
    }

    /**
     * جلب التفاصيل الشهرية لمكتب معين
     */
    private function getOfficeMonthlyDetails($officeId, $startDate, $endDate)
    {
        $monthlyDetails = [];
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            $monthStart = $currentDate->copy()->startOfMonth();
            $monthEnd = $currentDate->copy()->endOfMonth();

            // جلب إجمالي أسعار الحاويات للمكتب في الشهر
            $monthContainerRevenue = DB::table('customs_declarations as cd')
                ->join('containers as c', 'c.customs_id', '=', 'cd.id')
                ->where('cd.clearance_office_id', $officeId)
                ->whereBetween('c.transfer_date', [$monthStart, $monthEnd])
                ->whereNotNull('c.price')
                ->where('c.price', '>', 0)
                ->sum('c.price');

            // جلب إجمالي المعاملات المالية للمكتب في الشهر
            $monthTransactionRevenue = DailyTransaction::where('transactionable_type', User::class)
                ->where('transactionable_id', $officeId)
                ->where('type', 'income')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('total_amount');

            $monthTotalRevenue = (float) $monthContainerRevenue + (float) $monthTransactionRevenue;

            if ($monthTotalRevenue > 0) {
                // حساب الضريبة للشهر
                $monthTaxCalculation = $this->taxService->calculateTaxForOffice($officeId, $monthTotalRevenue);

                $monthlyDetails[] = [
                    'month_name' => $this->getArabicMonthName($currentDate->month) . ' ' . $currentDate->year,
                    'month_number' => $currentDate->month,
                    'container_revenue' => (float) $monthContainerRevenue,
                    'transaction_revenue' => (float) $monthTransactionRevenue,
                    'total_revenue' => $monthTotalRevenue,
                    'tax_rate' => $monthTaxCalculation['tax_rate'],
                    'tax_amount' => $monthTaxCalculation['tax_amount'],
                    'tax_enabled' => $monthTaxCalculation['tax_enabled']
                ];
            }

            $currentDate->addMonth();
        }

        return $monthlyDetails;
    }

    /**
     * حساب الضرائب المدفوعة من اليومية (المنصرف فقط) مجمعة حسب العلاقة
     */
    private function calculatePaidTaxes($startDate, $endDate)
    {
        $transactions = DailyTransaction::where('tax_value', '>', 0)
            ->where('type', 'expense') // فقط المعاملات من نوع منصرف
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with('transactionable')
            ->orderBy('created_at')
            ->get();

        // تقسيم المعاملات حسب الشهر وتجميعها حسب العلاقة
        $monthlyTransactions = [];
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            $monthStart = $currentDate->copy()->startOfMonth();
            $monthEnd = $currentDate->copy()->endOfMonth();

            $monthTransactions = $transactions->filter(function ($transaction) use ($monthStart, $monthEnd) {
                return $transaction->created_at->between($monthStart, $monthEnd);
            });

            if ($monthTransactions->count() > 0) {
                // تجميع المعاملات حسب العلاقة
                $groupedTransactions = $monthTransactions->groupBy(function ($transaction) {
                    return $transaction->transactionable_type . '_' . $transaction->transactionable_id;
                })->map(function ($group) {
                    $firstTransaction = $group->first();
                    return [
                        'transactionable_type' => $firstTransaction->transactionable_type,
                        'transactionable_id' => $firstTransaction->transactionable_id,
                        'transactionable_name' => $this->getTransactionableName($firstTransaction),
                        'transactions_count' => $group->count(),
                        'total_base_amount' => $group->sum('amount'),
                        'total_tax_amount' => $group->sum(function ($transaction) {
                            return $transaction->total_amount - $transaction->amount;
                        }),
                        'total_amount' => $group->sum('total_amount'),
                        'methods' => $group->pluck('method')->unique()->values()->toArray(),
                        'first_date' => $group->min('created_at'),
                        'last_date' => $group->max('created_at'),
                        'notes' => $group->pluck('notes')->filter()->unique()->values()->toArray()
                    ];
                })->values();

                $monthlyTransactions[] = [
                    'month_name' => $this->getArabicMonthName($currentDate->month) . ' ' . $currentDate->year,
                    'month_number' => $currentDate->month,
                    'grouped_transactions' => $groupedTransactions,
                    'month_total_tax' => $monthTransactions->sum(function ($transaction) {
                        return $transaction->total_amount - $transaction->amount;
                    }),
                    'month_total_amount' => $monthTransactions->sum('total_amount'),
                    'total_transactions_count' => $monthTransactions->count()
                ];
            }

            $currentDate->addMonth();
        }

        return [
            'monthly_data' => $monthlyTransactions,
            'total_tax_amount' => $transactions->sum(function ($transaction) {
                return $transaction->total_amount - $transaction->amount;
            }),
            'total_transactions_count' => $transactions->count()
        ];
    }

    /**
     * حساب الإحصائيات العامة
     */
    private function calculateTaxStats($collectedTaxes, $paidTaxes)
    {
        $totalCollectedTax = collect($collectedTaxes)->sum('tax_amount');
        $totalPaidTax = $paidTaxes['total_tax_amount'];
        $totalRevenue = collect($collectedTaxes)->sum('total_revenue');

        return [
            'total_collected_tax' => $totalCollectedTax,
            'total_paid_tax' => $totalPaidTax,
            'total_revenue' => $totalRevenue,
            'tax_difference' => $totalCollectedTax - $totalPaidTax,
            'offices_count' => count($collectedTaxes),
            'transactions_count' => $paidTaxes['total_transactions_count'],
            'monthly_data' => $paidTaxes['monthly_data']
        ];
    }

    /**
     * تصدير تقرير الضرائب
     */
    public function export(Request $request)
    {
        $year = $request->get('year', now()->year);
        $quarter = $request->get('quarter', $this->getCurrentQuarter());

        // نفس منطق index ولكن للتصدير
        $quarterDates = $this->getQuarterDates($year, $quarter);
        $clearanceOffices = $this->getActiveTaxOffices($quarterDates['start'], $quarterDates['end']);
        $collectedTaxes = $this->calculateCollectedTaxes($clearanceOffices, $quarterDates['start'], $quarterDates['end']);
        $paidTaxes = $this->calculatePaidTaxes($quarterDates['start'], $quarterDates['end']);
        $stats = $this->calculateTaxStats($collectedTaxes, $paidTaxes);

        return view('dashboard.taxes.export', compact(
            'year',
            'quarter',
            'quarterDates',
            'collectedTaxes',
            'paidTaxes',
            'stats'
        ));
    }

    /**
     * عرض تفاصيل المعاملات لعلاقة محددة
     */
    public function showTransactionDetails(Request $request)
    {
        $request->validate([
            'year' => 'required|integer',
            'quarter' => 'required|integer|between:1,4',
            'transactionable_type' => 'required|string',
            'transactionable_id' => 'required|integer',
            'month' => 'required|integer|between:1,12'
        ]);

        $year = $request->get('year');
        $quarter = $request->get('quarter');
        $transactionableType = $request->get('transactionable_type');
        $transactionableId = $request->get('transactionable_id');
        $month = $request->get('month');

        // تحديد تواريخ الشهر
        $monthStart = Carbon::create($year, $month, 1)->startOfMonth();
        $monthEnd = Carbon::create($year, $month, 1)->endOfMonth();

        // جلب المعاملات للعلاقة المحددة في الشهر المحدد
        $transactions = DailyTransaction::where('tax_value', '>', 0)
            ->where('type', 'expense')
            ->where('transactionable_type', $transactionableType)
            ->where('transactionable_id', $transactionableId)
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->with('transactionable')
            ->orderBy('created_at')
            ->get();

        // معلومات العلاقة
        $transactionableName = $this->getTransactionableName($transactions->first());

        // إحصائيات
        $stats = [
            'total_transactions' => $transactions->count(),
            'total_base_amount' => $transactions->sum('amount'),
            'total_tax_amount' => $transactions->sum(function ($transaction) {
                return $transaction->total_amount - $transaction->amount;
            }),
            'total_amount' => $transactions->sum('total_amount'),
            'methods' => $transactions->pluck('method')->unique()->values()->toArray(),
            'first_date' => $transactions->min('created_at'),
            'last_date' => $transactions->max('created_at')
        ];

        return view('dashboard.taxes.details', compact(
            'transactions',
            'transactionableName',
            'transactionableType',
            'transactionableId',
            'year',
            'quarter',
            'month',
            'monthStart',
            'monthEnd',
            'stats'
        ));
    }

    /**
     * الحصول على اسم الشهر بالعربية
     */
    public static function getArabicMonthName($monthNumber)
    {
        $months = [
            1 => 'يناير',
            2 => 'فبراير',
            3 => 'مارس',
            4 => 'أبريل',
            5 => 'مايو',
            6 => 'يونيو',
            7 => 'يوليو',
            8 => 'أغسطس',
            9 => 'سبتمبر',
            10 => 'أكتوبر',
            11 => 'نوفمبر',
            12 => 'ديسمبر'
        ];

        return $months[$monthNumber] ?? 'غير محدد';
    }

    /**
     * الحصول على اسم العلاقة
     */
    private function getTransactionableName($transaction)
    {
        if (!$transaction->transactionable) {
            return 'غير محدد';
        }

        // إذا كانت العلاقة مع User (مكتب تخليص)
        if ($transaction->transactionable_type === User::class) {
            return $transaction->transactionable->name . ' (مكتب تخليص)';
        }

        // إذا كانت العلاقة مع Car
        if ($transaction->transactionable_type === \App\Models\Car::class) {
            return $transaction->transactionable->name . ' (سيارة)';
        }

        // إذا كانت العلاقة مع Container
        if ($transaction->transactionable_type === \App\Models\Container::class) {
            return 'حاوية رقم: ' . $transaction->transactionable->container_number;
        }

        // إذا كانت العلاقة مع CustodyAccount
        if ($transaction->transactionable_type === \App\Models\CustodyAccount::class) {
            return $transaction->transactionable->name . ' (عهدة)';
        }

        // للعلاقات الأخرى، استخدم اسم الكلاس
        return class_basename($transaction->transactionable_type) . ' #' . $transaction->transactionable_id;
    }
}
