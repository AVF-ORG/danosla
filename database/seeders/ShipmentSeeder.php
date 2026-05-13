<?php

namespace Database\Seeders;

use App\Models\Shipment;
use App\Models\Lot;
use App\Models\User;
use Illuminate\Database\Seeder;

class ShipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all users with the 'shipper' role
        $shippers = User::role('shipper')->get();

        if ($shippers->isEmpty()) {
            $this->command->warn('No users with "shipper" role found. Skipping ShipmentSeeder.');
            return;
        }

        $this->command->info('Creating 50 shipments for ' . $shippers->count() . ' shippers...');

        Shipment::factory()
            ->count(50)
            ->make()
            ->each(function ($shipment) use ($shippers) {
                // Assign to one of the 3 shippers
                $shipment->user_id = $shippers->random()->id;
                
                // Mix of validity dates: 30% within 3 hours, 70% further away
                if (rand(1, 10) <= 3) {
                    $shipment->validity_date = now()->addHours(rand(1, 3))->addMinutes(rand(0, 59));
                } else {
                    $shipment->validity_date = now()->addDays(rand(1, 5));
                }
                
                $shipment->save();

                // Create exactly one lot for each shipment
                Lot::factory()
                    ->create([
                        'shipment_id' => $shipment->id,
                    ]);

                // Update shipment totals after lot is created
                $shipment->refresh();
                $shipment->update([
                    'total_volume' => $shipment->lot->volume,
                    'total_weight' => (float)$shipment->lot->weight * (int)$shipment->lot->quantity,
                ]);
            });
            
        $this->command->info('ShipmentSeeder completed successfully.');
    }
}
