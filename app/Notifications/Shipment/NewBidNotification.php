<?php

namespace App\Notifications\Shipment;

use App\Models\Shipment;
use App\Models\ShipmentBid;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class NewBidNotification extends Notification
{
    use Queueable;

    public $shipment;
    public $bid;

    /**
     * Create a new notification instance.
     */
    public function __construct(Shipment $shipment, ShipmentBid $bid)
    {
        $this->shipment = $shipment;
        $this->bid = $bid;
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
        $type = $this->bid->is_negotiable ? 'offre' : 'demande';
        $title = $this->bid->is_negotiable ? 'Nouvelle offre reçue' : 'Nouvelle demande reçue';

        return array_merge($this->shipment->toNotificationData(), [
            'bid_id' => $this->bid->id,
            'price' => $this->bid->price ? (number_format($this->bid->price, 2) . ' €') : (number_format($this->shipment->delivery_price ?? 0, 2) . ' €'),
            'shipper_name' => 'Transporteur',
            'title' => $title,
            'message' => "Un transporteur a envoyé une {$type} pour votre expédition vers {$this->shipment->delivery_address}.",
            'url' => route('transport-firm-bid.show', ['shipment' => $this->shipment->id, 'bid_id' => $this->bid->id]),
            'status_type' => 'bid_received',
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
