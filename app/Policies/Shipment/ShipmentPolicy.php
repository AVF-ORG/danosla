<?php

namespace App\Policies\Shipment;

use App\Models\Shipment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ShipmentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create shipments.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('shipper') || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can view the shipment.
     */
    public function view(User $user, Shipment $shipment): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('shipper')) {
            return $user->id === $shipment->user_id;
        }

        if ($user->hasRole('carrier')) {
            // Carriers can see all pending shipments to bid on them
            // OR any shipment they have already bid on (accepted or not)
            return $shipment->status === 'pending' || $shipment->bids()->where('user_id', $user->id)->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can update the shipment.
     */
    public function update(User $user, Shipment $shipment): bool
    {
        return $user->id === $shipment->user_id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the shipment.
     */
    public function delete(User $user, Shipment $shipment): bool
    {
        return $user->id === $shipment->user_id || $user->hasRole('admin');
    }
}
