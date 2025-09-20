<?php

namespace App\Http\Controllers\system;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\DailyTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CarExpensesController extends Controller
{
    /**
     * عرض صفحة مصروفات السيارات الرئيسية
     */
    public function index(Request $request)
    {
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);

        // تحديد تواريخ الشهر
        $monthStart = Carbon::create($year, $month, 1)->startOfMonth();
        $monthEnd = Carbon::create($year, $month, 1)->endOfMonth();

        // جلب جميع السيارات مع مصروفاتها الشهرية
        $cars = $this->getCarsWithMonthlyExpenses($monthStart, $monthEnd);

        // إحصائيات عامة
        $stats = $this->calculateCarExpensesStats($cars);

        return view('dashboard.expenses.cars.index', compact(
            'year',
            'month',
            'monthStart',
            'monthEnd',
            'cars',
            'stats'
        ));
    }

    /**
     * جلب السيارات مع مصروفاتها الشهرية
     */
    private function getCarsWithMonthlyExpenses($monthStart, $monthEnd)
    {
        $cars = Car::with(['dailyTransactions' => function ($query) use ($monthStart, $monthEnd) {
            $query->where('type', 'expense')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->orderBy('created_at');
        }])->get();

        return $cars->map(function ($car) use ($monthStart, $monthEnd) {
            $transactions = $car->dailyTransactions;

            // تجميع المعاملات حسب اليوم
            $dailyExpenses = $transactions->groupBy(function ($transaction) {
                return $transaction->created_at->format('Y-m-d');
            })->map(function ($dayTransactions) {
                return [
                    'date' => $dayTransactions->first()->created_at->format('Y-m-d'),
                    'count' => $dayTransactions->count(),
                    'total_amount' => $dayTransactions->sum('total_amount'),
                    'methods' => $dayTransactions->pluck('method')->unique()->values()->toArray(),
                    'transactions' => $dayTransactions
                ];
            });

            // إحصائيات الشهر
            $monthStats = [
                'total_transactions' => $transactions->count(),
                'total_amount' => $transactions->sum('total_amount'),
                'total_tax_amount' => $transactions->sum(function ($transaction) {
                    return $transaction->total_amount - $transaction->amount;
                }),
                'methods' => $transactions->pluck('method')->unique()->values()->toArray(),
                'first_date' => $transactions->min('created_at'),
                'last_date' => $transactions->max('created_at')
            ];

            return [
                'car' => $car,
                'daily_expenses' => $dailyExpenses,
                'month_stats' => $monthStats
            ];
        });
    }

    /**
     * حساب إحصائيات مصروفات السيارات
     */
    private function calculateCarExpensesStats($cars)
    {
        $totalCars = $cars->count();
        $carsWithExpenses = $cars->filter(function ($carData) {
            return $carData['month_stats']['total_transactions'] > 0;
        });

        $totalExpenses = $cars->sum(function ($carData) {
            return $carData['month_stats']['total_amount'];
        });

        $totalTransactions = $cars->sum(function ($carData) {
            return $carData['month_stats']['total_transactions'];
        });

        return [
            'total_cars' => $totalCars,
            'cars_with_expenses' => $carsWithExpenses->count(),
            'cars_without_expenses' => $totalCars - $carsWithExpenses->count(),
            'total_expenses' => $totalExpenses,
            'total_transactions' => $totalTransactions,
            'average_per_car' => $carsWithExpenses->count() > 0 ? $totalExpenses / $carsWithExpenses->count() : 0
        ];
    }

    /**
     * عرض تفاصيل مصروفات سيارة محددة
     */
    public function show(Request $request, Car $car)
    {
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);

        // تحديد تواريخ الشهر
        $monthStart = Carbon::create($year, $month, 1)->startOfMonth();
        $monthEnd = Carbon::create($year, $month, 1)->endOfMonth();

        // جلب معاملات السيارة في الشهر المحدد
        $transactions = DailyTransaction::where('transactionable_type', Car::class)
            ->where('transactionable_id', $car->id)
            ->where('type', 'expense')
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->orderBy('created_at')
            ->get();

        // إحصائيات الشهر
        $monthStats = [
            'total_transactions' => $transactions->count(),
            'total_amount' => $transactions->sum('total_amount'),
            'total_tax_amount' => $transactions->sum(function ($transaction) {
                return $transaction->total_amount - $transaction->amount;
            }),
            'methods' => $transactions->pluck('method')->unique()->values()->toArray(),
            'first_date' => $transactions->min('created_at'),
            'last_date' => $transactions->max('created_at')
        ];

        // جلب الأشهر السابقة التي تحتوي على معاملات
        $previousMonths = $this->getPreviousMonthsWithExpenses($car);

        return view('dashboard.expenses.cars.show', compact(
            'car',
            'transactions',
            'monthStats',
            'year',
            'month',
            'monthStart',
            'monthEnd',
            'previousMonths'
        ));
    }

    /**
     * جلب الأشهر السابقة التي تحتوي على معاملات للسيارة
     */
    private function getPreviousMonthsWithExpenses(Car $car)
    {
        $months = [];
        $currentDate = now()->startOfMonth();

        // جلب آخر 12 شهر
        for ($i = 0; $i < 12; $i++) {
            $monthStart = $currentDate->copy()->subMonths($i)->startOfMonth();
            $monthEnd = $currentDate->copy()->subMonths($i)->endOfMonth();

            $expenses = DailyTransaction::where('transactionable_type', Car::class)
                ->where('transactionable_id', $car->id)
                ->where('type', 'expense')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('total_amount');

            if ($expenses > 0) {
                $months[] = [
                    'year' => $monthStart->year,
                    'month' => $monthStart->month,
                    'month_name' => \App\Http\Controllers\system\TaxController::getArabicMonthName($monthStart->month) . ' ' . $monthStart->year,
                    'total_expenses' => $expenses,
                    'transactions_count' => DailyTransaction::where('transactionable_type', Car::class)
                        ->where('transactionable_id', $car->id)
                        ->where('type', 'expense')
                        ->whereBetween('created_at', [$monthStart, $monthEnd])
                        ->count()
                ];
            }
        }

        return $months;
    }
}
