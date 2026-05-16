<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shipment;
use App\Models\ShipmentBid;
use App\Models\BidMessage;
use App\Models\Review;
use App\Events\NewBidMessage;
use App\Events\BidUpdated;
use App\Notifications\ShipmentStatusChangedNotification;
use App\Notifications\NewBidNotification;
use Illuminate\Support\Facades\Notification;

class TransportFirmBidController extends Controller
{
    /**
     * Display a listing of the shipments.
     */
    public function index()
    {
        $user = auth()->user();
        $query = Shipment::query();

        if ($user->hasRole('carrier')) {
            // Carriers see non-expired pending shipments OR shipments where they have bid
            $query->where(function($q) use ($user) {
                $q->where(function($sq) {
                    $sq->where('status', 'pending')
                       ->where(function($vq) {
                           $vq->where('validity_date', '>=', now())
                              ->orWhereNull('validity_date');
                       });
                })
                ->orWhereHas('bids', function($bq) use ($user) {
                    $bq->where('user_id', $user->id);
                });
            });
        } elseif ($user->hasRole('shipper')) {
            // Shippers only see their own shipments
            $query->where('user_id', $user->id);
        }

        $shipments = $query->with('bids')->latest()->get();

        return view('pages.transport-firm-bid.index', [
            'title' => 'Transport Firm Bid - Shipments',
            'shipments' => $shipments
        ]);
    }

    /**
     * Show the form for creating a new transport firm bid.
     */
    public function create()
    {
        return view('pages.transport-firm-bid.create', [
            'title' => 'Create Transport Firm Bid'
        ]);
    }
    public function show(Request $request, Shipment $shipment)
    {
        $this->authorize('view', $shipment);
        $user = auth()->user();
        $bidId = $request->query('bid_id');
        $allBids = null;
        
        if ($user->hasRole('shipper') || $user->hasRole('admin')) {
            $allBids = $shipment->bids()->with(['user', 'messages'])->get()->map(function($bid) use ($user) {
                return [
                    'id' => $bid->id,
                    'price' => $bid->price,
                    'status' => $bid->status,
                    'updated_at_human' => $bid->updated_at->diffForHumans(null, true),
                    'unread_count' => $bid->messages->where('user_id', '!=', $user->id)->where('is_read', false)->count(),
                    'last_message' => $bid->messages->last()?->message ?? 'Pas de message'
                ];
            });
        }

        if ($bidId) {
            $myBid = ShipmentBid::where('shipment_id', $shipment->id)
                ->with(['messages.user'])
                ->find($bidId);
        } else {
            // For carriers, find their own bid
            $myBid = $shipment->bids()
                ->where('user_id', $user->id)
                ->with(['messages.user'])
                ->first();
                
            // For shippers, if no bid_id is selected, pick the first one or just show the list
            if (!$myBid && $allBids && $allBids->isNotEmpty()) {
                $myBid = ShipmentBid::with(['messages.user'])->find($allBids->first()['id']);
            }
        }
        
        if ($request->expectsJson()) {
            return response()->json([
                'bid' => $myBid,
                'messages' => $myBid ? $myBid->messages->map(fn($m) => [
                    'id' => $m->id,
                    'message' => $m->message,
                    'user_id' => $m->user_id,
                    'user_name' => $m->user_id === $user->id ? 'Vous' : ($user->hasRole('carrier') ? 'Client' : 'Transporteur'),
                    'created_at' => $m->created_at->format('H:i')
                ]) : []
            ]);
        }

        $shipment->load('lot');

        return view('pages.transport-firm-bid.show', [
            'title' => 'Détails de l\'expédition #'.str_pad($shipment->id, 5, '0', STR_PAD_LEFT),
            'shipment' => $shipment,
            'myBid' => $myBid,
            'allBids' => $allBids
        ]);
    }

    /**
     * Mark all messages in a bid as read for the current user.
     */
    public function markAsRead(ShipmentBid $bid)
    {
        $this->authorize('view', $bid->shipment);
        
        $bid->messages()
            ->where('user_id', '!=', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Show the form for editing the specified shipment.
     */
    public function edit(Shipment $shipment)
    {
        $shipment->load('lot');
        
        return view('pages.transport-firm-bid.edit', [
            'title' => 'Éditer l\'expédition #'.str_pad($shipment->id, 5, '0', STR_PAD_LEFT),
            'shipment' => $shipment
        ]);
    }

    /**
     * Store a new bid for the shipment.
     */
    public function storeBid(Request $request, Shipment $shipment)
    {
        $this->authorize('create', [ShipmentBid::class, $shipment]);

        $isNegotiable = $request->boolean('is_negotiable', true);
        
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

        if ($isNegotiable) {
            $validated = $request->validate([
                'price' => 'required|numeric|min:0',
                'latest_pickup_date' => 'required|date',
                'latest_pickup_time' => 'required',
                'latest_delivery_date' => 'required|date',
                'latest_delivery_time' => 'required',
                'message' => 'nullable|string',
            ]);
        } else {
            $validated = $request->validate([
                'message' => 'nullable|string',
            ]);
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

    /**
     * Store a new message for a specific bid.
     */
    public function storeMessage(Request $request, ShipmentBid $bid)
    {
        $this->authorize('negotiate', $bid);

        $validated = $request->validate([
            'message' => 'required|string',
        ]);

        $message = BidMessage::create([
            'bid_id' => $bid->id,
            'user_id' => auth()->id(),
            'message' => $validated['message'],
        ]);

        broadcast(new NewBidMessage($message->load('user')))->toOthers();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Message envoyé.',
                'bid_message' => $message->load('user'),
            ]);
        }

        return redirect()->back()->with('success', 'Message envoyé.');
    }

    public function acceptBid(ShipmentBid $bid)
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
        $otherCarriers = \App\Models\User::whereHas('bids', function($q) use ($shipment, $bid) {
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

    public function completeShipment(Shipment $shipment)
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

    public function storeReview(Request $request, Shipment $shipment)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

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

    /**
     * Remove the specified shipment from storage.
     */
    public function destroy(Shipment $shipment)
    {
        $this->authorize('delete', $shipment);
        // First delete associated lots to maintain database integrity
        $shipment->lot()->delete();
        $shipment->delete();

        return redirect()->route('transport-firm-bid.index')
            ->with('success', 'La demande d\'expédition a été supprimée avec succès.');
    }
}
