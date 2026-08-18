<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\AiUsageLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CompanyReportController extends Controller
{
    public function index(Request $request)
    {
        $companyId = $request->user()->company_id;

        if (!$companyId) {
            abort(403, 'User does not belong to any company.');
        }

        // Group usage by user within the company, hiding raw_cost
        $userUsage = User::where('company_id', $companyId)
            ->with(['aiUsageLogs' => function ($query) {
                $query->select(
                    'user_id',
                    'feature_name',
                    'provider_name',
                    DB::raw('SUM(billed_cost) as total_billed_cost'),
                    DB::raw('COUNT(*) as total_requests')
                )->groupBy('user_id', 'feature_name', 'provider_name');
            }])->get();

        return view('admin.company.index', compact('userUsage'));
    }
}
