<?php

namespace App\Services;

use App\Models\User;
use App\Models\OfficeTaxHistory;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class TaxCalculationService
{
    const DEFAULT_TAX_RATE = 15.00;

    /**
     * حساب الضرائب لمكتب في فترة معينة
     */
    public function calculateTaxForOffice($officeId, $amount, $date = null)
    {
        $date = $date ? Carbon::parse($date) : Carbon::now();

        // الحصول على حالة الضرائب في التاريخ المحدد
        $taxStatus = OfficeTaxHistory::getTaxStatusForDate($officeId, $date);

        // إذا لم يوجد سجل، استخدم الحالة الحالية للمكتب
        if (!$taxStatus) {
            $office = User::find($officeId);
            if (!$office || !$office->isClearanceOffice()) {
                return [
                    'original_amount' => $amount,
                    'tax_amount' => 0,
                    'total_amount' => $amount,
                    'tax_rate' => 0,
                    'tax_enabled' => false,
                    'tax_period' => null
                ];
            }

            // استخدام الحالة الحالية للمكتب
            $taxEnabled = $office->tax_enabled ?? true;
            $taxRate = self::DEFAULT_TAX_RATE;
        } else {
            $taxEnabled = $taxStatus->tax_enabled;
            $taxRate = $taxStatus->tax_rate ?? self::DEFAULT_TAX_RATE;
        }

        if (!$taxEnabled) {
            return [
                'original_amount' => $amount,
                'tax_amount' => 0,
                'total_amount' => $amount,
                'tax_rate' => 0,
                'tax_enabled' => false,
                'tax_period' => $taxStatus
            ];
        }

        $taxAmount = ($amount * $taxRate) / 100;

        return [
            'original_amount' => $amount,
            'tax_amount' => $taxAmount,
            'total_amount' => $amount + $taxAmount,
            'tax_rate' => $taxRate,
            'tax_enabled' => true,
            'tax_period' => $taxStatus
        ];
    }

    /**
     * حساب الضرائب لجميع المكاتب في فترة معينة
     */
    public function calculateTaxForAllOffices($amounts, $date = null)
    {
        $results = [];

        foreach ($amounts as $officeId => $amount) {
            $results[$officeId] = $this->calculateTaxForOffice($officeId, $amount, $date);
        }

        return $results;
    }

    /**
     * الحصول على إجمالي الضرائب لفترة معينة
     */
    public function getTotalTaxForPeriod($officeId, $startDate, $endDate)
    {
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);

        // الحصول على جميع فترات الضرائب في النطاق الزمني
        $taxPeriods = OfficeTaxHistory::where('office_id', $officeId)
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('effective_from', [$startDate, $endDate])
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->where('effective_from', '<=', $startDate)
                            ->where(function ($subQ) use ($endDate) {
                                $subQ->whereNull('effective_to')
                                    ->orWhere('effective_to', '>=', $endDate);
                            });
                    });
            })
            ->orderBy('effective_from')
            ->get();

        return $taxPeriods;
    }

    /**
     * حساب الضرائب بناءً على فترات مختلفة
     */
    public function calculateTaxWithPeriods($officeId, $amount, $startDate, $endDate)
    {
        $taxPeriods = $this->getTotalTaxForPeriod($officeId, $startDate, $endDate);

        if ($taxPeriods->isEmpty()) {
            return [
                'original_amount' => $amount,
                'tax_amount' => 0,
                'total_amount' => $amount,
                'tax_rate' => 0,
                'tax_enabled' => false,
                'periods' => []
            ];
        }

        $totalTaxAmount = 0;
        $periods = [];

        foreach ($taxPeriods as $period) {
            if ($period->tax_enabled) {
                $taxRate = $period->tax_rate ?? self::DEFAULT_TAX_RATE;
                $taxAmount = ($amount * $taxRate) / 100;
                $totalTaxAmount += $taxAmount;

                $periods[] = [
                    'period' => $period,
                    'tax_rate' => $taxRate,
                    'tax_amount' => $taxAmount,
                    'effective_from' => $period->effective_from,
                    'effective_to' => $period->effective_to
                ];
            }
        }

        return [
            'original_amount' => $amount,
            'tax_amount' => $totalTaxAmount,
            'total_amount' => $amount + $totalTaxAmount,
            'tax_rate' => $totalTaxAmount > 0 ? self::DEFAULT_TAX_RATE : 0,
            'tax_enabled' => $totalTaxAmount > 0,
            'periods' => $periods
        ];
    }

    /**
     * الحصول على ملخص الضرائب لمكتب في شهر معين
     */
    public function getMonthlyTaxSummary($officeId, $year, $month)
    {
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();

        $taxPeriods = $this->getTotalTaxForPeriod($officeId, $startDate, $endDate);

        $summary = [
            'office_id' => $officeId,
            'year' => $year,
            'month' => $month,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'tax_enabled_days' => 0,
            'tax_disabled_days' => 0,
            'total_days' => $startDate->diffInDays($endDate) + 1,
            'periods' => collect([])
        ];

        // إذا لم يوجد سجل، استخدم الحالة الحالية للمكتب
        if ($taxPeriods->isEmpty()) {
            $office = User::find($officeId);
            if ($office && $office->isClearanceOffice()) {
                $taxEnabled = $office->tax_enabled ?? true;
                $daysInMonth = $startDate->diffInDays($endDate) + 1;

                if ($taxEnabled) {
                    $summary['tax_enabled_days'] = $daysInMonth;
                } else {
                    $summary['tax_disabled_days'] = $daysInMonth;
                }

                $summary['periods']->push([
                    'period' => null,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'days' => $daysInMonth,
                    'tax_enabled' => $taxEnabled,
                    'tax_rate' => self::DEFAULT_TAX_RATE
                ]);
            }

            return $summary;
        }

        foreach ($taxPeriods as $period) {
            $periodStart = max($period->effective_from, $startDate);
            $periodEnd = $period->effective_to ? min($period->effective_to, $endDate) : $endDate;

            $daysInPeriod = $periodStart->diffInDays($periodEnd) + 1;

            if ($period->tax_enabled) {
                $summary['tax_enabled_days'] += $daysInPeriod;
            } else {
                $summary['tax_disabled_days'] += $daysInPeriod;
            }

            $summary['periods']->push([
                'period' => $period,
                'start_date' => $periodStart,
                'end_date' => $periodEnd,
                'days' => $daysInPeriod,
                'tax_enabled' => $period->tax_enabled,
                'tax_rate' => $period->tax_rate ?? self::DEFAULT_TAX_RATE
            ]);
        }

        return $summary;
    }
}
