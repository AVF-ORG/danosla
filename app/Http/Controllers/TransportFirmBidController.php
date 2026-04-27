<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shipment;
use App\Models\ShipmentBid;
use App\Models\BidMessage;
use App\Models\Review;

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
            // Carriers only see shipments where they have placed a bid
            $query->whereHas('bids', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        } elseif ($user->hasRole('shipper')) {
            // Shippers see all pending shipments AND shipments they created
            $query->where(function ($q) use ($user) {
                $q->where('status', 'pending')
                  ->orWhere('user_id', $user->id);
            });
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
        $user = auth()->user();
        $bidId = $request->query('bid_id');
        $allBids = null;
        
        if ($user->hasRole('shipper') || $user->hasRole('admin')) {
            $allBids = $shipment->bids()->with('user')->get();
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
                $myBid = $allBids->first();
                // Load messages for the default selected bid
                $myBid->load(['messages.user']);
            }
        }
        
        $shipment->load('lots');

        return view('pages.transport-firm-bid.show', [
            'title' => 'Détails de l\'expédition #'.str_pad($shipment->id, 5, '0', STR_PAD_LEFT),
            'shipment' => $shipment,
            'myBid' => $myBid,
            'allBids' => $allBids
        ]);
    }

    /**
     * Show the form for editing the specified shipment.
     */
    public function edit(Shipment $shipment)
    {
        $shipment->load('lots');
        
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
        $validated = $request->validate([
            'price' => 'required|numeric|min:0',
            'latest_pickup_date' => 'required|date',
            'latest_pickup_time' => 'required',
            'latest_delivery_date' => 'required|date',
            'latest_delivery_time' => 'required',
            'message' => 'nullable|string',
        ]);

        $bid = ShipmentBid::updateOrCreate(
            ['shipment_id' => $shipment->id, 'user_id' => auth()->id()],
            [
                'price' => $validated['price'],
                'latest_pickup_date' => $validated['latest_pickup_date'],
                'latest_pickup_time' => $validated['latest_pickup_time'],
                'latest_delivery_date' => $validated['latest_delivery_date'],
                'latest_delivery_time' => $validated['latest_delivery_time'],
                'status' => 'pending',
            ]
        );

        if (!empty($validated['message'])) {
            BidMessage::create([
                'bid_id' => $bid->id,
                'user_id' => auth()->id(),
                'message' => $validated['message'],
            ]);
        }

        return redirect()->back()->with('success', 'Votre proposition a été envoyée avec succès.');
    }

    /**
     * Store a new message for a specific bid.
     */
    public function storeMessage(Request $request, ShipmentBid $bid)
    {
        $validated = $request->validate([
            'message' => 'required|string',
        ]);

        BidMessage::create([
            'bid_id' => $bid->id,
            'user_id' => auth()->id(),
            'message' => $validated['message'],
        ]);

        return redirect()->back()->with('success', 'Message envoyé.');
    }

    public function acceptBid(ShipmentBid $bid)
    {
        $shipment = $bid->shipment;

        // Ensure only the shipment owner can accept bids
        if ($shipment->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            return back()->with('error', 'Unauthorized action.');
        }

        // 1. Accept this bid
        $bid->update(['status' => 'accepted']);

        // 2. Reject all other bids for this shipment
        $shipment->bids()->where('id', '!=', $bid->id)->update(['status' => 'rejected']);

        // 3. Update shipment status
        $shipment->update(['status' => 'active']);

        return back()->with('success', 'Offre acceptée avec succès. L\'expédition est maintenant active.');
    }

    public function completeShipment(Shipment $shipment)
    {
        // Ensure only the shipment owner can complete it
        if ($shipment->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            return back()->with('error', 'Unauthorized action.');
        }

        if ($shipment->status !== 'active') {
            return back()->with('error', 'Seules les expéditions actives peuvent être marquées comme terminées.');
        }

        $shipment->update(['status' => 'completed']);

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
        // First delete associated lots to maintain database integrity
        $shipment->lots()->delete();
        $shipment->delete();

        return redirect()->route('transport-firm-bid.index')
            ->with('success', 'La demande d\'expédition a été supprimée avec succès.');
    }
}
