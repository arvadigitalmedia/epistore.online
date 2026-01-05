<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\OrderDelivered;
use App\Models\User;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::with(['items', 'distributor'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }
        $order->load(['items.product', 'distributor']);
        return view('orders.show', compact('order'));
    }

    public function confirmReceipt(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if ($order->status !== Order::STATUS_SHIPPING && $order->status !== Order::STATUS_DELIVERED) {
             return redirect()->back()->with('error', 'Order cannot be confirmed at this stage.');
        }

        // Verification (OTP/Signature placeholder)
        // In real app, verify OTP here.

        $order->update([
            'status' => Order::STATUS_COMPLETED,
            'received_at' => now(),
        ]);

        // Notify Distributor (via their owner users)
        // Assuming distributor has users or we notify the distributor model if it's notifiable, 
        // but typically we notify Users.
        // For now, let's find the owner of the distributor.
        // Assuming Distributor model has 'owner' relationship or we find user with that distributor_id and owner role.
        // For simplicity, notify all users of that distributor for now, or just skip if complex.
        // Let's assume we notify the Distributor owner.
        
        $distributorUsers = User::where('distributor_id', $order->distributor_id)->get();
        foreach($distributorUsers as $user) {
             $user->notify(new OrderDelivered($order));
        }

        return redirect()->back()->with('success', 'Order confirmed as received.');
    }
}
