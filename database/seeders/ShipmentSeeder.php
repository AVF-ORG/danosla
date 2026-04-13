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
        // Get 3 random users with the 'shipper' role
        $shippers = User::role('shipper')->inRandomOrder()->take(3)->get();

        if ($shippers->isEmpty()) {
            $this->command->warn('No users with "shipper" role found. Skipping ShipmentSeeder.');
            return;
        }

        $this->command->info('Creating 20 shipments for ' . $shippers->count() . ' shippers...');

        Shipment::factory()
            ->count(20)
            ->make()
            ->each(function ($shipment) use ($shippers) {
                // Assign to one of the 3 shippers
                $shipment->user_id = $shippers->random()->id;
                $shipment->save();

                // Create 1-4 random lots for each shipment
                Lot::factory()
                    ->count(rand(1, 4))
                    ->create([
                        'shipment_id' => $shipment->id,
                    ]);

                // Update shipment totals after lots are created
                $shipment->refresh();
                $shipment->update([
                    'total_volume' => $shipment->lots->sum('volume'),
                    'total_weight' => $shipment->lots->sum(fn ($lot) => (float)$lot->weight * (int)$lot->quantity),
                ]);
            });
            
        $this->command->info('ShipmentSeeder completed successfully.');
    }
}
