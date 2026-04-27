<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shipment;
use App\Models\ShipmentBid;
use App\Models\BidMessage;

class TransportFirmBidController extends Controller
{
    /**
     * Display a listing of the shipments.
     */
    public function index()
    {
        // Fetch shipments (for now, simply all shipments ordered by newest first)
        $shipments = Shipment::latest()->get();

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
    public function show(Shipment $shipment)
    {
        // Load lots and the specific bid belonging to the authenticated carrier
        $shipment->load(['lots', 'bids' => function($query) {
            $query->where('user_id', auth()->id())->with(['messages' => function($q) {
                $q->oldest(); // Messages in chronological order
            }, 'messages.user']);
        }]);
        
        $myBid = $shipment->bids->first();

        return view('pages.transport-firm-bid.show', [
            'title' => 'Détails de l\'expédition #'.str_pad($shipment->id, 5, '0', STR_PAD_LEFT),
            'shipment' => $shipment,
            'myBid' => $myBid
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
