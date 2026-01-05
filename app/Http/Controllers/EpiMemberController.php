<?php

namespace App\Http\Controllers;

use App\Services\EpiIntegrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EpiMemberController extends Controller
{
    protected $epiService;

    public function __construct(EpiIntegrationService $epiService)
    {
        $this->epiService = $epiService;
    }

    public function show()
    {
        return view('member.upgrade', [
            'user' => Auth::user()
        ]);
    }

    public function upgrade(Request $request)
    {
        $user = Auth::user();

        if ($user->member_status === 'epi_channel') {
            return back()->with('info', 'Anda sudah terdaftar sebagai EPI Channel.');
        }

        // Validate API
        $isMember = $this->epiService->checkMemberStatus($user->email);

        if ($isMember) {
            $user->update([
                'member_status' => 'epi_channel',
                'member_verified_at' => now(),
            ]);
            
            // Assign role if using spatie/laravel-permission
            // $user->assignRole('epi_member');

            Log::info("User {$user->id} ({$user->email}) upgraded to EPI Channel.");

            // TODO: Send email notification

            return back()->with('success', 'Selamat! Akun Anda berhasil di-upgrade ke EPI Channel.');
        }

        return back()->with('error', 'Maaf, email Anda tidak terdaftar di sistem EPI Channel kami.');
    }
}
