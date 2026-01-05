<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use App\Models\Order;

class NewPickupOrder extends Notification
{
    use Queueable;

    protected $order;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'pickup_store_id' => $this->order->pickup_store_id,
            'pickup_at' => $this->order->pickup_at,
            'message' => 'Pesanan Ambil di Toko Baru #' . $this->order->order_number,
            'type' => 'pickup_order',
            'url' => route('orders.show', $this->order) // Assuming this route exists or will exist
        ];
    }
}
