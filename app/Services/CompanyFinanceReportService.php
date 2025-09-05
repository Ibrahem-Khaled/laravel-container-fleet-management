<?php

namespace App\Services;

use App\Models\DailyTransaction;
use Illuminate\Support\Collection;

class CompanyFinanceReportService
{
    public function build(int $year): array
    {
        $base = DailyTransaction::query()->whereYear('created_at', $year);

        // إجماليات عامة
        $totalIncome       = (clone $base)->where('type', 'income')->sum('total_amount');
        $totalExpense      = (clone $base)->where('type', 'expense')->sum('total_amount');
        $netProfit         = $totalIncome - $totalExpense;

        // الضرائب: وارد/منصرف
        $totalTaxIncome    = (clone $base)->where('type', 'income')->sum('tax_value');
        $totalTaxExpense   = (clone $base)->where('type', 'expense')->sum('tax_value');
        $totalTax          = $totalTaxExpense - $totalTaxIncome;

        // تفصيل حسب الفئة (transactionable_type)
        $byCategory = (clone $base)
            ->selectRaw('transactionable_type, type, SUM(total_amount) as total')
            ->groupBy('transactionable_type', 'type')
            ->get()
            ->groupBy('transactionable_type')
            ->map(function (Collection $rows, $type) {
                $income  = (float) ($rows->firstWhere('type', 'income')->total ?? 0);
                $expense = (float) ($rows->firstWhere('type', 'expense')->total ?? 0);
                return [
                    'category' => $this->labelFor($type),
                    'income'   => $income,
                    'expense'  => $expense,
                    'net'      => $income - $expense,
                ];
            })
            ->values();

        // مصفوفات شهرية (1..12)
        $monthlyIncome       = array_fill(1, 12, 0.0);
        $monthlyExpense      = array_fill(1, 12, 0.0);
        $monthlyTaxIncome    = array_fill(1, 12, 0.0);
        $monthlyTaxExpense   = array_fill(1, 12, 0.0);
        $monthlyNet          = array_fill(1, 12, 0.0);
        $cumulativeNet       = array_fill(1, 12, 0.0);

        // تجميع شهري للوارد (المبلغ والضريبة)
        $rowsIncome = (clone $base)
            ->where('type', 'income')
            ->selectRaw('MONTH(created_at) as m, SUM(total_amount) as amt, SUM(tax_value) as tax')
            ->groupBy('m')
            ->orderBy('m')
            ->get();

        foreach ($rowsIncome as $r) {
            $m = (int)$r->m;
            $monthlyIncome[$m]    = (float)$r->amt;
            $monthlyTaxIncome[$m] = (float)$r->tax;
        }

        // تجميع شهري للمنصرف (المبلغ والضريبة)
        $rowsExpense = (clone $base)
            ->where('type', 'expense')
            ->selectRaw('MONTH(created_at) as m, SUM(total_amount) as amt, SUM(tax_value) as tax')
            ->groupBy('m')
            ->orderBy('m')
            ->get();

        foreach ($rowsExpense as $r) {
            $m = (int)$r->m;
            $monthlyExpense[$m]    = (float)$r->amt;
            $monthlyTaxExpense[$m] = (float)$r->tax;
        }

        // احسب الصافي الشهري + الصافي التراكمي المترحِّل
        $running = 0.0;
        for ($m = 1; $m <= 12; $m++) {
            $monthlyNet[$m] = $monthlyIncome[$m] - $monthlyExpense[$m];
            $running += $monthlyNet[$m];
            $cumulativeNet[$m] = $running;
        }

        // تفاصيل شهرية منظمة للجدول
        $monthlyDetails = [];
        $monthNames = [1 => 'يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];
        for ($m = 1; $m <= 12; $m++) {
            $monthlyDetails[] = [
                'month_num'        => $m,
                'month_name'       => $monthNames[$m],
                'income'           => $monthlyIncome[$m],
                'expense'          => $monthlyExpense[$m],
                'net'              => $monthlyNet[$m],
                'cumulative_net'   => $cumulativeNet[$m],
                'tax_income'       => $monthlyTaxIncome[$m],
                'tax_expense'      => $monthlyTaxExpense[$m],
            ];
        }

        return [
            'totalIncome'        => (float)$totalIncome,
            'totalExpense'       => (float)$totalExpense,
            'netProfit'          => (float)$netProfit,
            'totalTax'           => (float)$totalTax,
            'totalTaxIncome'     => (float)$totalTaxIncome,
            'totalTaxExpense'    => (float)$totalTaxExpense,
            'byCategory'         => $byCategory,
            'monthlyIncome'      => $monthlyIncome,
            'monthlyExpense'     => $monthlyExpense,
            'monthlyNet'         => $monthlyNet,
            'cumulativeNet'      => $cumulativeNet,
            'monthlyTaxIncome'   => $monthlyTaxIncome,
            'monthlyTaxExpense'  => $monthlyTaxExpense,
            'monthlyDetails'     => $monthlyDetails,
        ];
    }

    private function labelFor(string $transactionableType): string
    {
        $map = [
            \App\Models\Car::class           => 'سيارات',
            // \App\Models\Employee::class      => 'مرتبات',
            \App\Models\Container::class     => 'حاويات',
            // \App\Models\CustomsOffice::class => 'مكاتب التخليص',
            // \App\Models\Yacht::class         => 'يخوت',
        ];
        return $map[$transactionableType] ?? class_basename($transactionableType);
    }
}
