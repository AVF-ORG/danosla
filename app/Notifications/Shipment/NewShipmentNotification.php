<?php

namespace App\Notifications\Shipment;

use App\Models\Shipment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class NewShipmentNotification extends Notification
{
    use Queueable;

    public $shipment;

    /**
     * Create a new notification instance.
     */
    public function __construct(Shipment $shipment)
    {
        $this->shipment = $shipment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return array_merge($this->shipment->toNotificationData(), [
            'shipper_name' => 'Expéditeur',
            'title' => 'Nouvelle expédition disponible',
            'message' => "De {$this->shipment->pickup_address} vers {$this->shipment->delivery_address}",
            'url' => route('transport-firm-bid.show', $this->shipment->id),
        ]);
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}
