<?php

namespace App\Http\Controllers\system;

use App\Http\Controllers\Controller;
use App\Models\AlhamdulillahExpense;
use App\Models\Container;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AlhamdulillahController extends Controller
{
    private const PASSWORD = 'alhamdulillah2024'; // كلمة السر

    public function index(Request $request)
    {
        // التحقق من كلمة السر
        if (!$request->session()->has('alhamdulillah_authenticated')) {
            return view('dashboard.alhamdulillah.login');
        }

        $year = (int) $request->get('year', now()->year);
        $month = (int) $request->get('month', now()->month);

        // جلب إجمالي الحاويات لكل شهر وسنة
        $containerStats = $this->getContainerStats($year, $month);

        // جلب بيانات المصروفات
        $expenseData = AlhamdulillahExpense::where('year', $year)
            ->where('month', $month)
            ->first();

        // تحديث المبلغ المصروف من أسعار الحاويات الفعلية إذا كانت البيانات موجودة
        if ($expenseData) {
            $actualSpentAmount = $this->getContainerPricesForMonth($year, $month);
            if ($expenseData->spent_amount != $actualSpentAmount) {
                $expenseData->spent_amount = $actualSpentAmount;
                $expenseData->save();
            }
        }

        // سنوات متاحة
        $years = range(now()->year, now()->year - 5);

        // أشهر السنة
        $months = [
            1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
            5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
            9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'
        ];

        return view('dashboard.alhamdulillah.index', compact(
            'containerStats',
            'expenseData',
            'year',
            'month',
            'years',
            'months'
        ));
    }

    public function authenticate(Request $request)
    {
        $password = $request->input('password');

        if ($password === self::PASSWORD) {
            $request->session()->put('alhamdulillah_authenticated', true);
            return redirect()->route('alhamdulillah.index')
                ->with('success', 'مرحباً بك في صفحة الحمد لله');
        }

        return back()->withErrors(['password' => 'كلمة السر غير صحيحة']);
    }

    public function logout(Request $request)
    {
        $request->session()->forget('alhamdulillah_authenticated');
        return redirect()->route('alhamdulillah.index')
            ->with('info', 'تم تسجيل الخروج بنجاح');
    }

    public function setupExpense(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|min:2020|max:2030',
            'month' => 'required|integer|min:1|max:12',
            'amount_per_container' => 'required|numeric|min:0',
        ]);

        $year = $request->year;
        $month = $request->month;
        $amountPerContainer = $request->amount_per_container;

        // جلب عدد الحاويات للشهر والسنة المحددة
        $containerCount = $this->getContainerCountForMonth($year, $month);

        // جلب إجمالي أسعار الحاويات لنفس الشهر (المبلغ المصروف التلقائي)
        $spentAmount = $this->getContainerPricesForMonth($year, $month);

        $totalAmount = $containerCount * $amountPerContainer;

        // إنشاء أو تحديث بيانات المصروفات
        $expenseData = AlhamdulillahExpense::updateOrCreate(
            ['year' => $year, 'month' => $month],
            [
                'container_count' => $containerCount,
                'amount_per_container' => $amountPerContainer,
                'total_amount' => $totalAmount,
                'spent_amount' => $spentAmount,
                'notes' => $request->notes
            ]
        );

        return redirect()->route('alhamdulillah.index', ['year' => $year, 'month' => $month])
            ->with('success', "تم إعداد المصروفات للشهر: {$expenseData->month_name} {$year}");
    }

    public function details(Request $request)
    {
        $year = (int) $request->get('year', now()->year);
        $month = (int) $request->get('month', now()->month);

        $expenseData = AlhamdulillahExpense::where('year', $year)
            ->where('month', $month)
            ->first();

        if (!$expenseData) {
            return redirect()->route('alhamdulillah.index')
                ->withErrors(['error' => 'لم يتم إعداد المصروفات لهذا الشهر بعد']);
        }

        // تحديث المبلغ المصروف من أسعار الحاويات الفعلية
        $actualSpentAmount = $this->getContainerPricesForMonth($year, $month);
        if ($expenseData->spent_amount != $actualSpentAmount) {
            $expenseData->spent_amount = $actualSpentAmount;
            $expenseData->save();
        }

        return view('dashboard.alhamdulillah.details', compact('expenseData', 'year', 'month'));
    }

    private function getContainerStats($year, $month)
    {
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();

        // إجمالي الحاويات للشهر المحدد
        $monthlyCount = Container::whereBetween('created_at', [$startDate, $endDate])->count();

        // إجمالي الحاويات للسنة
        $yearlyCount = Container::whereYear('created_at', $year)->count();

        // إجمالي الحاويات لكل شهر في السنة
        $monthlyStats = Container::whereYear('created_at', $year)
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        return [
            'monthly_count' => $monthlyCount,
            'yearly_count' => $yearlyCount,
            'monthly_stats' => $monthlyStats
        ];
    }

    private function getContainerCountForMonth($year, $month)
    {
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();

        return Container::whereBetween('created_at', [$startDate, $endDate])->count();
    }

    private function getContainerPricesForMonth($year, $month)
    {
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();

        return Container::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('price')
            ->where('price', '>', 0)
            ->sum('price');
    }
}
