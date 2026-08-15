<?php

namespace App\Http\Controllers\Shipment\Actions;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shipment\StoreMessageRequest;
use App\Models\ShipmentBid;
use App\Models\BidMessage;
use App\Events\Shipment\NewBidMessage;
use App\Notifications\Shipment\NewMessageNotification;

class StoreMessageAction extends Controller
{
    /**
     * Store a new message for a specific bid.
     */
    public function __invoke(StoreMessageRequest $request, ShipmentBid $bid)
    {
        $validated = $request->validated();

        $message = BidMessage::create([
            'bid_id' => $bid->id,
            'user_id' => auth()->id(),
            'message' => $validated['message'],
        ]);

        broadcast(new NewBidMessage($message->load('user')))->toOthers();

        // --- Notify Recipient ---
        $recipient = auth()->id() === $bid->user_id ? $bid->shipment->user : $bid->user;
        $recipient->notify(new NewMessageNotification($bid->shipment, $bid, $message));
        // ------------------------

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Message envoyé.',
                'bid_message' => $message->load('user'),
            ]);
        }

        return redirect()->back()->with('success', 'Message envoyé.');
    }
}
