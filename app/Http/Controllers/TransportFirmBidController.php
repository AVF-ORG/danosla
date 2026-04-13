<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shipment;

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
    /**
     * Display the specified shipment.
     */
    public function show(Shipment $shipment)
    {
        $shipment->load('lots');
        
        return view('pages.transport-firm-bid.show', [
            'title' => 'Détails de l\'expédition #'.str_pad($shipment->id, 5, '0', STR_PAD_LEFT),
            'shipment' => $shipment
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
