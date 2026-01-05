<?php

namespace App\Http\Controllers\Distributor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\OrderShipped;
use App\Notifications\OrderStatusUpdated;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->distributor_id) {
            abort(403, 'User is not associated with a distributor.');
        }

        $query = Order::with(['user', 'items'])
            ->where('distributor_id', $user->distributor_id);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $orders = $query->latest()->paginate(10)->withQueryString();

        return view('distributor.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $this->authorizeDistributor($order);
        $order->load(['user', 'items.product', 'pickupStore']);
        return view('distributor.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $this->authorizeDistributor($order);

        $request->validate([
            'status' => 'required|in:pending,processing,shipping,delivered,completed,cancelled',
            'notes' => 'nullable|string'
        ]);

        $order->update([
            'status' => $request->status,
            'notes' => $request->notes ? $order->notes . "\n[" . now()->format('Y-m-d H:i') . "] " . $request->notes : $order->notes,
        ]);

        // Trigger Notification
        $order->user->notify(new OrderStatusUpdated($order));

        return redirect()->back()->with('success', 'Order status updated successfully.');
    }

    public function updateShipping(Request $request, Order $order)
    {
        $this->authorizeDistributor($order);

        $request->validate([
            'shipping_courier' => 'required|string|max:255',
            'shipping_tracking_number' => 'required|string|max:255',
            'estimated_delivery_date' => 'nullable|date',
        ]);

        $order->update([
            'shipping_courier' => $request->shipping_courier,
            'shipping_tracking_number' => $request->shipping_tracking_number,
            'estimated_delivery_date' => $request->estimated_delivery_date,
            'status' => Order::STATUS_SHIPPING,
            'shipped_at' => now(),
        ]);

        $order->user->notify(new OrderShipped($order));

        return redirect()->back()->with('success', 'Shipping information updated successfully.');
    }

    public function cancel(Request $request, Order $order)
    {
        $this->authorizeDistributor($order);

        $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        $order->update([
            'status' => Order::STATUS_CANCELLED,
            'notes' => $order->notes . "\n[CANCELLED] Reason: " . $request->cancellation_reason,
        ]);

        // Restore Stock
        foreach ($order->items as $item) {
             $stockQuery = \App\Models\ProductStock::where('distributor_id', $order->distributor_id)
                    ->where('product_id', $item->product_id);
                
             if ($order->pickup_store_id) {
                 $stockQuery->where('store_location_id', $order->pickup_store_id);
             } else {
                 $stockQuery->whereNull('store_location_id');
             }
             $stockQuery->increment('quantity', $item->quantity);
        }

        return redirect()->back()->with('success', 'Order cancelled successfully.');
    }
    
    public function invoice(Order $order)
    {
        $this->authorizeDistributor($order);
        $order->load(['user', 'items.product', 'distributor']);
        
        // Reuse the user invoice view or create a specific one for print
        // For now, let's reuse the one we created but add a 'print' mode or just rely on browser print
        return view('orders.invoice', compact('order'));
    }

    private function authorizeDistributor(Order $order)
    {
        if (Auth::user()->distributor_id !== $order->distributor_id) {
            abort(403, 'Unauthorized access to this order.');
        }
    }
}
