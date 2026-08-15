<?php

namespace App\Policies\Shipment;

use App\Models\Shipment;
use App\Models\ShipmentBid;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ShipmentBidPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the bid.
     */
    public function view(User $user, ShipmentBid $bid): bool
    {
        return $user->hasRole('admin') ||
               $user->id === $bid->user_id ||
               $user->id === $bid->shipment->user_id;
    }

    /**
     * Determine whether the user can create a bid on a shipment.
     */
    public function create(User $user, Shipment $shipment): bool
    {
        // Only carriers can bid, and only on pending shipments
        return $user->hasRole('carrier') && $shipment->status === 'pending';
    }

    /**
     * Determine whether the user can accept the bid.
     */
    public function accept(User $user, ShipmentBid $bid): bool
    {
        // Only the shipment owner or admin can accept bids
        return $user->hasRole('admin') || $user->id === $bid->shipment->user_id;
    }

    /**
     * Determine whether the user can negotiate (send messages).
     */
    public function negotiate(User $user, ShipmentBid $bid): bool
    {
        // 1. Must be the bidder, shipment owner, or admin
        if (!$this->view($user, $bid)) {
            return false;
        }

        $shipment = $bid->shipment;

        // 2. If shipment is active or completed, always allow messaging for coordination
        if (in_array($shipment->status, ['active', 'completed'])) {
            return true;
        }

        // 3. If shipment is still pending, only allow negotiation if the bid is
        // negotiable AND we're within the shipment's negotiation window (see
        // Shipment::isWithinNegotiationWindow() — the single source of truth
        // for this time rule, shared with Shipment::can_negotiate).
        return $bid->is_negotiable && $shipment->isWithinNegotiationWindow();
    }
}
