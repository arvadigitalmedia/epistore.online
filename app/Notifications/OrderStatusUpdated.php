<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->line('The status of your order #' . $this->order->order_number . ' has been updated.')
                    ->line('New Status: ' . ucfirst($this->order->status))
                    ->action('View Order', route('orders.show', $this->order->id))
                    ->line('Thank you for shopping with us!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'message' => 'Status Order #' . $this->order->order_number . ' updated to ' . ucfirst($this->order->status),
            'link' => route('orders.show', $this->order->id),
        ];
    }
}
