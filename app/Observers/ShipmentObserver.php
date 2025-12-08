<?php

namespace App\Observers;

use App\Models\Shipment;

class ShipmentObserver
{
    public function created(Shipment $shipment): void
    {
        // Update order status when shipment is created
        if ($shipment->order) {
            $shipment->order->update(['status' => 'shipped']);
        }
    }

    public function updated(Shipment $shipment): void
    {
        // Update order status based on shipment status
        if ($shipment->wasChanged('status') && $shipment->order) {
            if ($shipment->status === 'delivered') {
                $shipment->order->update(['status' => 'delivered']);
            }
        }
    }
}






