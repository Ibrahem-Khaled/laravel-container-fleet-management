<?php

namespace App\Http\Controllers\system;

use App\Http\Controllers\Controller;
use App\Services\CompanyFinanceReportService;
use Illuminate\Http\Request;

class CompanyFinanceController extends Controller
{
    public function index(Request $request, CompanyFinanceReportService $service)
    {
        $year = (int)($request->get('year') ?: now()->year);
        $report = $service->build($year);

        $years = range(now()->year, now()->year - 5);

        return view('dashboard.company.finance', [
            'year'               => $year,
            'years'              => $years,
            'totalIncome'        => $report['totalIncome'],
            'totalExpense'       => $report['totalExpense'],
            'netProfit'          => $report['netProfit'],
            'totalTax'           => $report['totalTax'],
            'totalTaxIncome'     => $report['totalTaxIncome'],
            'totalTaxExpense'    => $report['totalTaxExpense'],
            'byCategory'         => $report['byCategory'],
            'monthlyIncome'      => $report['monthlyIncome'],
            'monthlyExpense'     => $report['monthlyExpense'],
            'monthlyNet'         => $report['monthlyNet'],
            'cumulativeNet'      => $report['cumulativeNet'],
            'monthlyTaxIncome'   => $report['monthlyTaxIncome'],
            'monthlyTaxExpense'  => $report['monthlyTaxExpense'],
            'monthlyDetails'     => $report['monthlyDetails'],
        ]);
    }
}
