<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class OrderPaymentController extends Controller
{
    /**
     * Display the order invoice page.
     */
    public function invoice(Order $order)
    {
        $this->authorizeOrder($order);
        
        // Load relationships needed for invoice
        $order->load(['items.product', 'distributor']);
        
        return view('orders.invoice', compact('order'));
    }

    /**
     * Display the payment instruction and upload page.
     */
    public function payment(Order $order)
    {
        $this->authorizeOrder($order);

        if ($order->payment_status !== 'pending') {
            return redirect()->route('orders.confirmation', $order)
                ->with('info', 'Pembayaran untuk pesanan ini sudah diproses.');
        }

        return view('orders.payment', compact('order'));
    }

    /**
     * Handle payment proof upload.
     */
    public function storePayment(Request $request, Order $order)
    {
        $this->authorizeOrder($order);

        $request->validate([
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg,pdf|max:2048',
            'bank_sender' => 'nullable|string|max:255', // Optional: Nama bank pengirim
            'account_name' => 'nullable|string|max:255', // Optional: Nama pemilik rekening
        ]);

        if ($request->hasFile('payment_proof')) {
            $path = $request->file('payment_proof')->store('payment_proofs', 'public');
            
            $order->update([
                'payment_proof_path' => $path,
                'payment_status' => 'processing', // Menunggu verifikasi
                'status' => Order::STATUS_PROCESSING, // Update main status as well? Or keep pending until verified?
                // Let's assume processing means "Admin is checking".
                // If main status flow is Pending -> Processing -> Shipping -> Delivered.
                // Pending = Unpaid/New.
                // Processing = Paid/Verifying/Packing.
            ]);
            
            // Log activity or notify admin could happen here
        }

        return redirect()->route('orders.confirmation', $order)
            ->with('success', 'Bukti pembayaran berhasil diunggah. Pesanan Anda sedang diproses.');
    }

    /**
     * Display the confirmation page after payment/order placement.
     */
    public function confirmation(Order $order)
    {
        $this->authorizeOrder($order);

        return view('orders.confirmation', compact('order'));
    }

    /**
     * Ensure the user owns the order.
     */
    private function authorizeOrder(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this order.');
        }
    }
}
