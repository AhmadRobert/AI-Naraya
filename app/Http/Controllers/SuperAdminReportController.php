<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Company;
use App\Models\AiUsageLog;
use Illuminate\Support\Facades\DB;

class SuperAdminReportController extends Controller
{
    public function index()
    {
        // Get all companies for the dropdown / management list
        $companies = Company::all();

        // Total costs across all companies
        $totals = AiUsageLog::select(
            DB::raw('SUM(raw_cost) as total_raw_cost'),
            DB::raw('SUM(billed_cost) as total_billed_cost')
        )->first();

        // Group usage by company
        $companyUsage = Company::with(['aiUsageLogs' => function ($query) {
            $query->select(
                'company_id',
                DB::raw('SUM(raw_cost) as total_raw_cost'),
                DB::raw('SUM(billed_cost) as total_billed_cost'),
                DB::raw('COUNT(*) as total_requests')
            )->groupBy('company_id');
        }])->get();

        return view('admin.super.index', compact('totals', 'companyUsage', 'companies'));
    }

    public function storeCompany(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'markup_percentage' => 'required|numeric|min:0|max:1000',
        ]);

        Company::create([
            'name' => $validated['name'],
            'markup_percentage' => $validated['markup_percentage'],
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'UMKM baru berhasil ditambahkan.');
    }

    public function updateMarkup(Request $request, Company $company)
    {
        $validated = $request->validate([
            'markup_percentage' => 'required|numeric|min:0|max:1000',
        ]);

        $company->update(['markup_percentage' => $validated['markup_percentage']]);

        return redirect()->back()->with('success', 'Markup UMKM berhasil diperbarui.');
    }

    public function toggleStatus(Company $company)
    {
        $company->update(['is_active' => !$company->is_active]);
        
        $statusStr = $company->is_active ? 'diaktifkan' : 'diblokir';
        return redirect()->back()->with('success', "UMKM berhasil {$statusStr}.");
    }
}
