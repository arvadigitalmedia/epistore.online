<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderShipped extends Notification implements ShouldQueue
{
    use Queueable;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function via(object $notifiable): array
    {
        return ['database']; // Using database notification for now (easiest to demo without mail setup)
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->line('Your order has been shipped!')
                    ->line('Courier: ' . $this->order->shipping_courier)
                    ->line('Tracking Number: ' . $this->order->shipping_tracking_number)
                    ->action('View Order', route('orders.show', $this->order->id))
                    ->line('Thank you for shopping with us!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'message' => 'Order #' . $this->order->order_number . ' has been shipped via ' . $this->order->shipping_courier,
            'link' => route('orders.show', $this->order->id),
        ];
    }
}
