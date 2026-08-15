<?php

namespace App\Http\Controllers\Shipment\Actions;

use App\Http\Controllers\Controller;
use App\Models\ShipmentBid;
use App\Models\User;
use App\Events\Shipment\BidUpdated;
use App\Notifications\Shipment\ShipmentStatusChangedNotification;
use Illuminate\Support\Facades\Notification;

class AcceptBidAction extends Controller
{
    /**
     * Accept a bid, rejecting all other bids for the shipment.
     */
    public function __invoke(ShipmentBid $bid)
    {
        $this->authorize('accept', $bid);

        $shipment = $bid->shipment;

        // 1. Accept this bid
        $bid->update(['status' => 'accepted']);

        // 2. Reject all other bids for this shipment
        $shipment->bids()->where('id', '!=', $bid->id)->update(['status' => 'rejected']);

        // 3. Update shipment status
        $shipment->update(['status' => 'active']);

        // --- Notify Carriers ---
        // 1. Notify the accepted carrier
        $bid->user->notify(new ShipmentStatusChangedNotification($shipment, 'accepted'));

        // 2. Notify all other carriers who had bids/demands
        $otherCarriers = User::whereHas('bids', function ($q) use ($shipment, $bid) {
            $q->where('shipment_id', $shipment->id)->where('id', '!=', $bid->id);
        })->get();

        Notification::send($otherCarriers, new ShipmentStatusChangedNotification($shipment, 'rejected'));
        // -----------------------

        broadcast(new BidUpdated($bid))->toOthers();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Offre acceptée avec succès. L\'expédition est maintenant active.',
                'bid' => $bid,
                'shipment_status' => $shipment->status,
            ]);
        }

        return back()->with('success', 'Offre acceptée avec succès. L\'expédition est maintenant active.');
    }
}
