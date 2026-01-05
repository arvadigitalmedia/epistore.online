<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EpiIntegrationService
{
    /**
     * Check if email exists in external EPI system.
     * 
     * @param string $email
     * @return bool
     */
    public function checkMemberStatus(string $email): bool
    {
        // Placeholder for real API call
        // In production, this would be:
        // $response = Http::withToken(config('services.epi.token'))
        //     ->get(config('services.epi.url') . '/api/check-member', ['email' => $email]);
        // return $response->successful() && $response->json('is_member');

        // Mock Logic:
        // For testing, we assume any email containing "epi" or "member" is valid,
        // or if it matches a specific test email.
        
        // Simulating API latency
        // sleep(1);

        if (str_contains($email, 'epi') || str_contains($email, 'member')) {
            return true;
        }

        return false;
    }
}
