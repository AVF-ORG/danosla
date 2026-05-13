<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('bid.{bidId}', function ($user, $bidId) {
    if ($user->hasRole('admin')) return true;
    
    $bid = \App\Models\ShipmentBid::find($bidId);
    if (!$bid) return false;
    
    // Accessible by the carrier who made the bid OR the shipper who owns the shipment
    return $user->id === $bid->user_id || $user->id === $bid->shipment->user_id;
});

Broadcast::channel('shipment.{shipmentId}', function ($user, $shipmentId) {
    if ($user->hasRole('admin')) return true;

    $shipment = \App\Models\Shipment::find($shipmentId);
    if (!$shipment) return false;
    
    // Accessible by the shipper who owns the shipment OR any carrier who has a bid on it
    return $user->id === $shipment->user_id || $shipment->bids()->where('user_id', $user->id)->exists();
});
