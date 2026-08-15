<?php

namespace App\Http\Controllers\Shipment\Actions;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shipment\StoreReviewRequest;
use App\Models\Shipment;
use App\Models\Review;

class StoreReviewAction extends Controller
{
    /**
     * Store a review left by the shipper for the carrier of a completed shipment.
     */
    public function __invoke(StoreReviewRequest $request, Shipment $shipment)
    {
        $validated = $request->validated();

        // Ensure only the shipment owner can rate
        if ($shipment->user_id !== auth()->id()) {
            return back()->with('error', 'Seul le client peut laisser un avis.');
        }

        // Ensure shipment is completed
        if ($shipment->status !== 'completed') {
            return back()->with('error', 'L\'expédition doit être terminée avant de laisser un avis.');
        }

        // Check if review already exists
        if ($shipment->review()->exists()) {
            return back()->with('error', 'Vous avez déjà laissé un avis pour cette expédition.');
        }

        // Find the accepted carrier
        $acceptedBid = $shipment->bids()->where('status', 'accepted')->first();
        if (!$acceptedBid) {
            return back()->with('error', 'Aucun transporteur accepté trouvé.');
        }

        Review::create([
            'shipment_id' => $shipment->id,
            'reviewer_id' => auth()->id(),
            'reviewee_id' => $acceptedBid->user_id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
        ]);

        return back()->with('success', 'Merci pour votre avis !');
    }
}
