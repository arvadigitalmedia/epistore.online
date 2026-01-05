<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Courier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class CourierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Courier::query();

        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%');
        }

        $couriers = $query->orderBy('priority')->paginate(20);
        return view('admin.couriers.index', compact('couriers'));
    }

    /**
     * Handle bulk actions.
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:couriers,id',
            'action' => 'required|in:activate,deactivate',
        ]);

        $status = $request->action === 'activate';
        $count = Courier::whereIn('id', $request->ids)->update(['is_active' => $status]);

        Log::info('Bulk courier status update', [
            'action' => $request->action,
            'count' => $count,
            'ids' => $request->ids,
            'user_id' => Auth::id()
        ]);

        return back()->with('success', "$count kurir berhasil di" . ($status ? 'aktifkan' : 'nonaktifkan') . ".");
    }

    /**
     * Update the status of the specified resource.
     */
    public function toggleStatus(Courier $courier)
    {
        $oldStatus = $courier->is_active;
        $courier->update(['is_active' => !$courier->is_active]);
        
        Log::info('Courier status updated', [
            'courier' => $courier->code,
            'old_status' => $oldStatus,
            'new_status' => $courier->is_active,
            'user_id' => Auth::id()
        ]);

        return back()->with('success', 'Status kurir berhasil diperbarui.');
    }
    
    /**
     * Update priority.
     */
    public function updatePriority(Request $request, Courier $courier)
    {
        $request->validate(['priority' => 'required|integer']);
        
        $oldPriority = $courier->priority;
        $courier->update(['priority' => $request->priority]);

        Log::info('Courier priority updated', [
            'courier' => $courier->code,
            'old_priority' => $oldPriority,
            'new_priority' => $courier->priority,
            'user_id' => Auth::id()
        ]);

        return back()->with('success', 'Prioritas kurir berhasil diperbarui.');
    }
}
