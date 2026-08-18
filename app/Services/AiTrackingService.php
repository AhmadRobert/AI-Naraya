<?php

namespace App\Services;

use App\Models\AiUsageLog;
use App\Models\User;

class AiTrackingService
{
    /**
     * Log AI Usage and calculate billed cost based on company markup.
     *
     * @param User $user
     * @param string $featureName
     * @param string $providerName
     * @param float $rawCost
     * @param string $status
     * @return AiUsageLog
     */
    public function logUsage(User $user, string $featureName, string $providerName, float $rawCost, string $status = 'success'): AiUsageLog
    {
        $billedCost = $rawCost;
        $companyId = $user->company_id;

        if ($user->role !== 'super_admin' && $user->company) {
            $markupPercentage = $user->company->markup_percentage ?? 50.00;
            $billedCost = $rawCost + ($rawCost * ($markupPercentage / 100));
        }

        return AiUsageLog::create([
            'user_id' => $user->id,
            'company_id' => $companyId,
            'feature_name' => $featureName,
            'provider_name' => $providerName,
            'raw_cost' => $rawCost,
            'billed_cost' => $billedCost,
            'status' => $status,
        ]);
    }
}
