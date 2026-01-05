<?php

namespace App\Http\Controllers\Distributor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if (!$user->distributor_id) {
            abort(403, 'User is not associated with a distributor.');
        }

        $distributor = $user->distributor;
        
        // Stats
        $stats = [
            'pending_orders' => Order::where('distributor_id', $user->distributor_id)
                ->where('status', 'pending')
                ->count(),
            'processing_orders' => Order::where('distributor_id', $user->distributor_id)
                ->where('status', 'processing')
                ->count(),
            'completed_orders' => Order::where('distributor_id', $user->distributor_id)
                ->where('status', 'completed')
                ->count(),
        ];

        return view('distributor.dashboard', compact('distributor', 'stats'));
    }
}
