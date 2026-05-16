<?php

namespace App\Notifications\Shipment;

use App\Models\Shipment;
use App\Models\ShipmentBid;
use App\Models\BidMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class NewMessageNotification extends Notification
{
    use Queueable;

    public $shipment;
    public $bid;
    public $message;

    /**
     * Create a new notification instance.
     */
    public function __construct(Shipment $shipment, ShipmentBid $bid, BidMessage $message)
    {
        $this->shipment = $shipment;
        $this->bid = $bid;
        $this->message = $message;
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
        $sender = auth()->user();
        $senderName = $sender->hasRole('carrier') ? 'Transporteur' : 'Expéditeur';

        return array_merge($this->shipment->toNotificationData(), [
            'bid_id' => $this->bid->id,
            'message_id' => $this->message->id,
            'shipper_name' => $senderName,
            'title' => 'Nouveau message',
            'message' => "Vous avez reçu un nouveau message concernant l'expédition vers {$this->shipment->delivery_address}.",
            'url' => route('transport-firm-bid.show', ['shipment' => $this->shipment->id, 'bid_id' => $this->bid->id]),
            'status_type' => 'message_received',
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
