<?php

namespace App\Notifications;

use App\Models\Shipment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class ShipmentStatusChangedNotification extends Notification
{
    use Queueable;

    public $shipment;
    public $type; // 'accepted', 'rejected', 'completed', 'cancelled'

    /**
     * Create a new notification instance.
     */
    public function __construct(Shipment $shipment, string $type)
    {
        $this->shipment = $shipment;
        $this->type = $type;
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
        $statusLabels = [
            'accepted' => 'Offre acceptée',
            'rejected' => 'Plus disponible',
            'completed' => 'Expédition terminée',
            'cancelled' => 'Expédition annulée',
        ];

        $messages = [
            'accepted' => "Félicitations ! Votre offre pour l'expédition vers {$this->shipment->delivery_address} a été acceptée.",
            'rejected' => "L'expédition vers {$this->shipment->delivery_address} n'est plus disponible.",
            'completed' => "L'expédition vers {$this->shipment->delivery_address} a été marquée comme terminée.",
            'cancelled' => "L'expédition vers {$this->shipment->delivery_address} a été annulée.",
        ];

        return [
            'shipment_id' => $this->shipment->id,
            'pickup_address' => $this->shipment->pickup_address,
            'delivery_address' => $this->shipment->delivery_address,
            'pickup_at' => ($this->shipment->latest_pickup_date?->format('d/m') ?? '--') . ' ' . ($this->shipment->latest_pickup_time ?? '--'),
            'delivery_at' => ($this->shipment->latest_delivery_date?->format('d/m') ?? '--') . ' ' . ($this->shipment->latest_delivery_time ?? '--'),
            'validity_at' => $this->shipment->validity_date?->format('d/m H:i') ?? '--',
            'price' => number_format($this->shipment->delivery_price ?? 0, 2) . ' €',
            'shipper_name' => 'Système',
            'title' => $statusLabels[$this->type] ?? 'Mise à jour d\'expédition',
            'message' => $messages[$this->type] ?? "Le statut de l'expédition a changé.",
            'url' => route('transport-firm-bid.show', $this->shipment->id),
            'status_type' => $this->type,
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}
