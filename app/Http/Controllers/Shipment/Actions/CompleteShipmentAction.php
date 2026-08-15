<?php

namespace App\Http\Controllers\Shipment\Actions;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Notifications\Shipment\ShipmentStatusChangedNotification;

class CompleteShipmentAction extends Controller
{
    /**
     * Mark an active shipment as completed.
     */
    public function __invoke(Shipment $shipment)
    {
        $this->authorize('update', $shipment);

        if ($shipment->status !== 'active') {
            return back()->with('error', 'Seules les expéditions actives peuvent être marquées comme terminées.');
        }

        $shipment->update(['status' => 'completed']);

        // --- Notify Carrier ---
        $acceptedBid = $shipment->bids()->where('status', 'accepted')->first();
        if ($acceptedBid) {
            $acceptedBid->user->notify(new ShipmentStatusChangedNotification($shipment, 'completed'));
        }
        // ----------------------

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Expédition terminée ! Vous pouvez maintenant laisser un avis au transporteur.',
                'shipment_status' => $shipment->status,
            ]);
        }

        return back()->with('success', 'Expédition terminée ! Vous pouvez maintenant laisser un avis au transporteur.');
    }
}
