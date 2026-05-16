<?php

namespace App\Notifications;

use App\Models\Shipment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
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
        return [
            'shipment_id' => $this->shipment->id,
            'pickup_address' => $this->shipment->pickup_address,
            'delivery_address' => $this->shipment->delivery_address,
            'pickup_at' => ($this->shipment->latest_pickup_date?->format('d/m') ?? '--') . ' ' . ($this->shipment->latest_pickup_time ?? '--'),
            'delivery_at' => ($this->shipment->latest_delivery_date?->format('d/m') ?? '--') . ' ' . ($this->shipment->latest_delivery_time ?? '--'),
            'validity_at' => $this->shipment->validity_date?->format('d/m H:i') ?? '--',
            'price' => number_format($this->shipment->delivery_price ?? 0, 2) . ' €',
            'shipper_name' => 'Expéditeur',
            'title' => 'Nouvelle expédition disponible',
            'message' => "De {$this->shipment->pickup_address} vers {$this->shipment->delivery_address}",
            'url' => route('transport-firm-bid.show', $this->shipment->id),
        ];
    }
}
