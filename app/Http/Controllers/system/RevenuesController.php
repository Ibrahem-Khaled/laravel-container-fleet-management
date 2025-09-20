<?php

namespace App\Http\Controllers\system;

use App\Http\Controllers\Controller;
use App\Models\Container;
use App\Models\CustomsDeclaration;
use App\Models\Role;
use App\Models\User;
use App\Services\TaxCalculationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RevenuesController extends Controller
{
    /**
     * عرض قائمة مكاتب التخليص مع إحصائيات عامة
     */
    public function index(Request $request)
    {
        // فلاتر اختيارية
        $startDate = $request->query('start_date');
        $endDate   = $request->query('end_date');

        // جلب كيان Role لمكتب التخليص
        $clearanceRole = Role::query()->where('name', 'clearance_office')->firstOrFail();

        $officesQuery = User::query()
            ->withRoleModel($clearanceRole)
            ->withCount([
                'containers as containers_count' => function ($q) use ($startDate, $endDate) {
                    $q->transported()->withinTransferDate($startDate, $endDate);
                },
            ])
            ->withSum([
                'containers as containers_total_price' => function ($q) use ($startDate, $endDate) {
                    $q->transported()->withinTransferDate($startDate, $endDate);
                }
            ], 'price')
            ->withSum([
                'dailyTransactions as income_sum' => function ($q) use ($startDate, $endDate) {
                    $q->where('type', 'income')->withinDateRange($startDate, $endDate);
                }
            ], 'total_amount')
            ->withMax([
                'containers as last_container_date' => function ($q) use ($startDate, $endDate) {
                    $q->transported()->withinTransferDate($startDate, $endDate);
                }
            ], 'transfer_date');

        // استنساخ الكويري قبل الترتيب والتقسيم لحساب الإجماليات
        $statsQuery = clone $officesQuery;

        $offices = $officesQuery
            ->orderByDesc('last_container_date')
            ->paginate(20)
            ->through(function ($user) {
                $totalPrice = (int) ($user->containers_total_price ?? 0);
                $income     = (float) ($user->income_sum ?? 0.0);
                $user->required_amount = $totalPrice - $income;

                // حساب الضرائب
                $taxService = new TaxCalculationService();
                $taxCalculation = $taxService->calculateTaxForOffice($user->id, $user->required_amount);
                $user->tax_calculation = $taxCalculation;

                return $user;
            });

        // حساب الإحصاءات من الكويري المستنسخ
        $statsData = $statsQuery->get();
        $stats = [
            'offices_count'    => $statsData->count(),
            'total_containers' => $statsData->sum('containers_count'),
            'total_prices'     => $statsData->sum('containers_total_price'),
            'total_income'     => $statsData->sum('income_sum'),
        ];
        $stats['total_required'] = $stats['total_prices'] - $stats['total_income'];


        return view('dashboard.revenues.clearance_offices.index', compact('offices', 'stats', 'startDate', 'endDate'));
    }

    public function monthly(Request $request, User $office)
    {
        $request->validate([
            'year'  => 'nullable|integer|min:2020|max:' . now()->year,
            'month' => 'nullable|integer|min:1|max:12',
        ]);

        $year  = (int) $request->input('year', now()->year);
        $month = (int) $request->input('month', now()->month);
        $reportDate = Carbon::create($year, $month, 1);

        $monthStart = $reportDate->copy()->startOfMonth()->toDateString();
        $monthEnd   = $reportDate->copy()->endOfMonth()->toDateString();

        // إقرارات هذا المكتب خلال الشهر (بناءً على تاريخ البيان)
        $declarations = CustomsDeclaration::query()
            ->where('clearance_office_id', $office->id)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->with('containers')
            ->withSum('containers as containers_sum_price', 'price')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($declaration) {
                $declaration->calculated_price = (float) ($declaration->containers_sum_price ?? 0);
                return $declaration;
            });

        // وارد اليومية (دخل فقط)
        $incomeTransactions = $office->dailyTransactions()
            ->where('type', 'income')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('created_at', 'desc')
            ->get();

        // إجمالي أسعار الحاويات المنقولة خلال الشهر
        $transportedContainersSum = Container::query()
            ->whereHas('customs', fn($q) => $q->where('clearance_office_id', $office->id))
            ->transported()
            ->withinTransferDate($monthStart, $monthEnd)
            ->sum('price');

        $totalValue  = (float) $declarations->sum('calculated_price');
        $totalIncome = (float) $incomeTransactions->sum('total_amount');
        $balance     = (float) $transportedContainersSum - $totalIncome;

        // حساب الضرائب للمكتب في هذا الشهر
        $taxService = new TaxCalculationService();
        $taxCalculation = $taxService->calculateTaxForOffice($office->id, $balance, $reportDate);
        $monthlyTaxSummary = $taxService->getMonthlyTaxSummary($office->id, $year, $month);

        return view('dashboard.revenues.clearance_offices.monthly', compact(
            'office',
            'reportDate',
            'declarations',
            'incomeTransactions',
            'totalValue',
            'totalIncome',
            'transportedContainersSum',
            'balance',
            'taxCalculation',
            'monthlyTaxSummary'
        ));
    }

    public function yearly(Request $request, User $office)
    {
        $request->validate([
            'year' => 'nullable|integer|min:2020|max:' . now()->year,
        ]);

        $year = (int) $request->input('year', now()->year);

        $containersByMonth = Container::query()
            ->whereHas('customs', fn($q) => $q->where('clearance_office_id', $office->id))
            ->transported()
            ->whereYear('transfer_date', $year)
            ->selectRaw('MONTH(transfer_date) as m, SUM(price) as total')
            ->groupBy('m')
            ->pluck('total', 'm');

        $incomeByMonth = $office->dailyTransactions()
            ->where('type', 'income')
            ->whereYear('created_at', $year)
            ->selectRaw('MONTH(created_at) as m, SUM(total_amount) as total')
            ->groupBy('m')
            ->pluck('total', 'm');

        $rows = collect(range(1, 12))->map(function ($m) use ($containersByMonth, $incomeByMonth, $office, $year) {
            $transported = (float) ($containersByMonth[$m] ?? 0);
            $income      = (float) ($incomeByMonth[$m] ?? 0);
            $balance     = $transported - $income;

            // حساب الضرائب لكل شهر
            $taxService = new TaxCalculationService();
            $monthDate = Carbon::create($year, $m, 1);
            $taxCalculation = $taxService->calculateTaxForOffice($office->id, $balance, $monthDate);

            return compact('m') + [
                'month'       => $m,
                'transported' => $transported,
                'income'      => $income,
                'balance'     => $balance,
                'tax_calculation' => $taxCalculation,
            ];
        });

        $yearTotals = [
            'transported' => $rows->sum('transported'),
            'income'      => $rows->sum('income'),
            'balance'     => $rows->sum('balance'),
            'tax_amount'  => $rows->sum('tax_calculation.tax_amount'),
            'total_with_tax' => $rows->sum('tax_calculation.total_amount'),
        ];

        return view('dashboard.revenues.clearance_offices.yearly', [
            'office'      => $office,
            'year'        => $year,
            'rows'        => $rows,
            'yearTotals'  => $yearTotals,
        ]);
    }

    public function bulkUpdateDeclarationContainersPrice(
        Request $request,
        User $office,
        CustomsDeclaration $declaration
    ) {
        abort_unless($declaration->clearance_office_id === $office->id, 404);

        $data = $request->validate([
            'price' => ['required', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($declaration, $data) {
            $declaration->containers()->update(['price' => (int) $data['price']]);
        });

        return back()->with('status', 'تم تحديث أسعار الحاويات لهذا البيان بنجاح.');
    }

    public function updateSingleContainerPrice(
        Request $request,
        User $office,
        Container $container
    ) {
        abort_unless(optional($container->customs)->clearance_office_id === $office->id, 404);

        $data = $request->validate([
            'price' => ['required', 'numeric', 'min:0'],
        ]);

        $container->update(['price' => (int) $data['price']]);

        return back()->with('status', 'تم تحديث سعر الحاوية بنجاح.');
    }
}
