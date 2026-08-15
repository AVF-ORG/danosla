<?php

namespace App\Http\Controllers\Shipment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shipment;
use App\Models\ShipmentBid;

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
