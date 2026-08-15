<?php

namespace App\Http\Controllers\Shipment\Actions;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shipment\StoreBidRequest;
use App\Models\Shipment;
use App\Models\ShipmentBid;
use App\Models\BidMessage;
use App\Events\Shipment\NewBidMessage;
use App\Events\Shipment\BidUpdated;
use App\Notifications\Shipment\NewBidNotification;

class StoreBidAction extends Controller
{
    /**
     * Store a new bid for the shipment.
     */
    public function __invoke(StoreBidRequest $request, Shipment $shipment)
    {
        $isNegotiable = $request->isNegotiable();

        if ($shipment->is_expired) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Cette expédition a expiré.'], 422);
            }
            return redirect()->back()->with('error', 'Cette expédition a expiré.');
        }

        if ($isNegotiable && !$shipment->can_negotiate) {
            return redirect()->back()->with('error', 'La négociation n\'est pas autorisée pour cette expédition.');
        }

        if (!$isNegotiable && !$shipment->can_demand) {
            return redirect()->back()->with('error', 'L\'envoi de demande n\'est plus possible.');
        }

        $validated = $request->validated();

        if (!$isNegotiable) {
            // For direct request, we leave terms as NULL as requested
            $validated['price'] = null;
            $validated['latest_pickup_date'] = null;
            $validated['latest_pickup_time'] = null;
            $validated['latest_delivery_date'] = null;
            $validated['latest_delivery_time'] = null;
        }

        $bid = ShipmentBid::updateOrCreate(
            ['shipment_id' => $shipment->id, 'user_id' => auth()->id()],
            [
                'price' => $validated['price'],
                'latest_pickup_date' => $validated['latest_pickup_date'],
                'latest_pickup_time' => $validated['latest_pickup_time'],
                'latest_delivery_date' => $validated['latest_delivery_date'],
                'latest_delivery_time' => $validated['latest_delivery_time'],
                'is_negotiable' => $isNegotiable,
                'status' => 'pending',
            ]
        );

        if (!empty($validated['message'])) {
            // Only allow messages if negotiable OR if it's the initial message of a direct request (optional, but keeping it for now if provided)
            $message = BidMessage::create([
                'bid_id' => $bid->id,
                'user_id' => auth()->id(),
                'message' => $validated['message'],
            ]);

            broadcast(new NewBidMessage($message->load('user')))->toOthers();
        }

        broadcast(new BidUpdated($bid))->toOthers();

        // --- Notify Shipper ---
        $shipment->user->notify(new NewBidNotification($shipment, $bid));
        // ----------------------

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $isNegotiable ? 'Votre proposition a été envoyée.' : 'Votre demande directe a été envoyée.',
                'bid' => $bid->load('user', 'messages'),
            ]);
        }

        $message = $isNegotiable ? 'Votre proposition a été envoyée.' : 'Votre demande directe a été envoyée.';
        return redirect()->back()->with('success', $message);
    }
}
