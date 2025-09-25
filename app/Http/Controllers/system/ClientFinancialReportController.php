<?php

namespace App\Http\Controllers\system;

use App\Http\Controllers\Controller;
use App\Services\ClientFinancialReportService;
use App\Models\User;
use Illuminate\Http\Request;

class ClientFinancialReportController extends Controller
{
    public function __construct()
    {
        // Constructor can be empty for now
    }

    /**
     * عرض تقرير المستحقات المالية للعملاء
     * الضريبة تُحسب على إجمالي أسعار الحاويات وأوامر النقل
     * الصافي الإجمالي = الوارد من اليومية - (إجمالي الحاويات + أوامر النقل + الضرائب)
     */
    public function index(Request $request)
    {
        $year = (int)($request->get('year') ?: now()->year);
        $month = (int)($request->get('month') ?: now()->month);

        // إنشاء الخدمة مباشرة
        $reportService = app(ClientFinancialReportService::class);

        // جلب مكاتب التخليص
        $clearanceOffices = User::whereHas('role', function ($query) {
            $query->where('name', 'clearance_office');
        })->with('role')->get();

        // حساب المستحقات لكل مكتب
        $officeReports = [];
        foreach ($clearanceOffices as $office) {
            $report = $reportService->calculateOfficeDueAmount($office->id, $year, $month);
            $officeReports[] = [
                'office' => $office,
                'report' => $report
            ];
        }

        // حساب الإجماليات
        $totals = $reportService->calculateTotals($officeReports);

        // سنوات متاحة للفلترة
        $years = range(now()->year, now()->year - 5);

        // أشهر السنة
        $months = [
            1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
            5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
            9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'
        ];

        return view('dashboard.reports.client-financial', compact(
            'officeReports',
            'totals',
            'year',
            'month',
            'years',
            'months'
        ));
    }

    /**
     * تصدير التقرير إلى PDF أو Excel
     */
    public function export(Request $request)
    {
        $year = (int)($request->get('year') ?: now()->year);
        $month = (int)($request->get('month') ?: now()->month);
        $format = $request->get('format', 'pdf');

        // إنشاء الخدمة مباشرة
        $reportService = app(ClientFinancialReportService::class);

        // نفس منطق index ولكن للتصدير
        $clearanceOffices = User::whereHas('role', function ($query) {
            $query->where('name', 'clearance_office');
        })->with('role')->get();

        $officeReports = [];
        foreach ($clearanceOffices as $office) {
            $report = $reportService->calculateOfficeDueAmount($office->id, $year, $month);
            $officeReports[] = [
                'office' => $office,
                'report' => $report
            ];
        }

        $totals = $reportService->calculateTotals($officeReports);

        if ($format === 'excel') {
            return $this->exportToExcel($officeReports, $totals, $year, $month);
        }

        return $this->exportToPdf($officeReports, $totals, $year, $month);
    }

    private function exportToExcel($officeReports, $totals, $year, $month)
    {
        // TODO: تنفيذ تصدير Excel
        return response()->json(['message' => 'Excel export not implemented yet']);
    }

    private function exportToPdf($officeReports, $totals, $year, $month)
    {
        // TODO: تنفيذ تصدير PDF
        return response()->json(['message' => 'PDF export not implemented yet']);
    }
}
