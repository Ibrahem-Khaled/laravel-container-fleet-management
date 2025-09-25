<?php

namespace App\Services;

use App\Models\User;
use App\Models\Container;
use App\Models\DailyTransaction;
use App\Services\TaxCalculationService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ClientFinancialReportService
{
    protected TaxCalculationService $taxService;

    public function __construct(TaxCalculationService $taxService)
    {
        $this->taxService = $taxService;
    }

    /**
     * حساب المبلغ المستحق لمكتب تخليص في شهر وسنة معينة
     * الضريبة تُحسب على إجمالي أسعار الحاويات وأوامر النقل
     * الصافي الإجمالي = الوارد من اليومية - (إجمالي الحاويات + أوامر النقل + الضرائب)
     */
    public function calculateOfficeDueAmount(int $officeId, int $year, int $month): array
    {
        $startDate = Carbon::create($year, $month, 1)->startOfDay();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth()->endOfDay();

        // 1. إجمالي أسعار الحاويات للمكتب في الشهر
        $containerRevenue = DB::table('customs_declarations as cd')
            ->join('containers as c', 'c.customs_id', '=', 'cd.id')
            ->where('cd.clearance_office_id', $officeId)
            ->whereBetween('c.transfer_date', [$startDate, $endDate])
            ->whereNotNull('c.price')
            ->where('c.price', '>', 0)
            ->sum('c.price');

        // 2. إجمالي أوامر النقل للمكتب في الشهر
        $transferOrdersRevenue = DB::table('container_transfer_orders as cto')
            ->join('containers as c', 'c.id', '=', 'cto.container_id')
            ->join('customs_declarations as cd', 'cd.id', '=', 'c.customs_id')
            ->where('cd.clearance_office_id', $officeId)
            ->whereBetween('cto.created_at', [$startDate, $endDate])
            ->whereNotNull('cto.price')
            ->where('cto.price', '>', 0)
            ->sum('cto.price');

        // 3. إجمالي الوارد من المكتب في اليومية في الشهر
        $dailyIncome = DailyTransaction::where('transactionable_type', User::class)
            ->where('transactionable_id', $officeId)
            ->where('type', 'income')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_amount');

        // 4. حساب المبلغ المستحق
        $totalRevenue = (float) $containerRevenue + (float) $transferOrdersRevenue;
        $dueAmount = $totalRevenue - (float) $dailyIncome;

        // 5. حساب الضرائب على إجمالي أسعار الحاويات وأوامر النقل إذا كان المكتب مفعل الضرائب
        $taxCalculation = $this->taxService->calculateTaxForOffice($officeId, $totalRevenue, $startDate);

        // 6. حساب المبلغ شامل الضريبة (الضريبة على إجمالي الإيرادات وليس على المستحق فقط)
        $amountWithTax = $totalRevenue + $taxCalculation['tax_amount'];

        // 7. حساب الصافي الإجمالي = الوارد من اليومية - (إجمالي الحاويات + أوامر النقل + الضرائب)
        $netAmountAfterTax = (float) $dailyIncome - $amountWithTax;

        return [
            'container_revenue' => (float) $containerRevenue,
            'transfer_orders_revenue' => (float) $transferOrdersRevenue,
            'total_revenue' => $totalRevenue,
            'daily_income' => (float) $dailyIncome,
            'due_amount' => $dueAmount,
            'tax_enabled' => $taxCalculation['tax_enabled'],
            'tax_rate' => $taxCalculation['tax_rate'],
            'tax_amount' => $taxCalculation['tax_amount'],
            'amount_with_tax' => $amountWithTax,
            'net_amount_after_tax' => $netAmountAfterTax,
            'period' => [
                'year' => $year,
                'month' => $month,
                'start_date' => $startDate,
                'end_date' => $endDate
            ]
        ];
    }

    /**
     * حساب الإجماليات لجميع المكاتب
     */
    public function calculateTotals(array $officeReports): array
    {
        $totals = [
            'total_container_revenue' => 0,
            'total_transfer_orders_revenue' => 0,
            'total_revenue' => 0,
            'total_daily_income' => 0,
            'total_due_amount' => 0,
            'total_tax_amount' => 0,
            'total_amount_with_tax' => 0,
            'total_net_amount_after_tax' => 0,
            'offices_count' => count($officeReports),
            'offices_with_tax' => 0,
            'offices_without_tax' => 0
        ];

        foreach ($officeReports as $officeReport) {
            $report = $officeReport['report'];

            $totals['total_container_revenue'] += $report['container_revenue'];
            $totals['total_transfer_orders_revenue'] += $report['transfer_orders_revenue'];
            $totals['total_revenue'] += $report['total_revenue'];
            $totals['total_daily_income'] += $report['daily_income'];
            $totals['total_due_amount'] += $report['due_amount'];
            $totals['total_tax_amount'] += $report['tax_amount'];
            $totals['total_amount_with_tax'] += $report['amount_with_tax'];
            $totals['total_net_amount_after_tax'] += $report['net_amount_after_tax'];

            if ($report['tax_enabled']) {
                $totals['offices_with_tax']++;
            } else {
                $totals['offices_without_tax']++;
            }
        }

        return $totals;
    }

    /**
     * حساب المستحقات التراكمية حتى الشهر الحالي (بدون الشهر الحالي)
     */
    public function calculateCumulativeDueAmount(int $officeId, int $year, int $month): array
    {
        $startOfYear = Carbon::create($year, 1, 1)->startOfDay();
        $endOfPreviousMonth = Carbon::create($year, $month, 1)->subDay()->endOfDay();

        // إجمالي أسعار الحاويات من بداية السنة حتى نهاية الشهر السابق
        $containerRevenue = DB::table('customs_declarations as cd')
            ->join('containers as c', 'c.customs_id', '=', 'cd.id')
            ->where('cd.clearance_office_id', $officeId)
            ->whereBetween('c.transfer_date', [$startOfYear, $endOfPreviousMonth])
            ->whereNotNull('c.price')
            ->where('c.price', '>', 0)
            ->sum('c.price');

        // إجمالي أوامر النقل من بداية السنة حتى نهاية الشهر السابق
        $transferOrdersRevenue = DB::table('container_transfer_orders as cto')
            ->join('containers as c', 'c.id', '=', 'cto.container_id')
            ->join('customs_declarations as cd', 'cd.id', '=', 'c.customs_id')
            ->where('cd.clearance_office_id', $officeId)
            ->whereBetween('cto.created_at', [$startOfYear, $endOfPreviousMonth])
            ->whereNotNull('cto.price')
            ->where('cto.price', '>', 0)
            ->sum('cto.price');

        // إجمالي الوارد من المكتب في اليومية من بداية السنة حتى نهاية الشهر السابق
        $dailyIncome = DailyTransaction::where('transactionable_type', User::class)
            ->where('transactionable_id', $officeId)
            ->where('type', 'income')
            ->whereBetween('created_at', [$startOfYear, $endOfPreviousMonth])
            ->sum('total_amount');

        $totalRevenue = (float) $containerRevenue + (float) $transferOrdersRevenue;
        $cumulativeDueAmount = $totalRevenue - (float) $dailyIncome;

        return [
            'container_revenue' => (float) $containerRevenue,
            'transfer_orders_revenue' => (float) $transferOrdersRevenue,
            'total_revenue' => $totalRevenue,
            'daily_income' => (float) $dailyIncome,
            'cumulative_due_amount' => $cumulativeDueAmount,
            'period' => [
                'year' => $year,
                'month' => $month,
                'start_date' => $startOfYear,
                'end_date' => $endOfPreviousMonth
            ]
        ];
    }

    /**
     * الحصول على اسم الشهر بالعربية
     */
    public static function getArabicMonthName(int $monthNumber): string
    {
        $months = [
            1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
            5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
            9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'
        ];

        return $months[$monthNumber] ?? 'غير محدد';
    }
}
