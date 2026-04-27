<?php

namespace Database\Seeders;

use App\Models\Shipment;
use App\Models\ShipmentBid;
use App\Models\BidMessage;
use App\Models\User;
use Illuminate\Database\Seeder;

class ShipmentBidSeeder extends Seeder
{
    public function run(): void
    {
        $shipments = Shipment::all();
        $carriers = User::role('carrier')->get();

        if ($shipments->isEmpty() || $carriers->isEmpty()) {
            return;
        }

        foreach ($shipments as $shipment) {
            // For testing, we make EVERY carrier bid on EVERY shipment
            // This ensures no matter which user you log in as, you see data.
            
            // Also include the Admin (User #1) in the list of bidders
            $allBidders = $carriers->collect();
            if (User::find(1) && !$allBidders->pluck('id')->contains(1)) {
                $allBidders->push(User::find(1));
            }

            foreach ($allBidders as $carrier) {
                $bid = ShipmentBid::updateOrCreate(
                    ['shipment_id' => $shipment->id, 'user_id' => $carrier->id],
                    [
                        'price' => rand(300, 1500),
                        'latest_pickup_date' => now()->addDays(rand(1, 5)),
                        'latest_pickup_time' => '09:00',
                        'latest_delivery_date' => now()->addDays(rand(6, 10)),
                        'latest_delivery_time' => '17:00',
                        'status' => 'pending',
                    ]
                );

                // Create a conversation for every bid
                $messageCount = rand(3, 6);
                $parent = null;

                for ($i = 0; $i < $messageCount; $i++) {
                    $senderId = ($i % 2 === 0) ? $carrier->id : $shipment->user_id;
                    if (!$senderId) continue;

                    $parent = BidMessage::create([
                        'bid_id' => $bid->id,
                        'user_id' => $senderId,
                        'parent_id' => $parent ? $parent->id : null,
                        'message' => $this->getRandomMessage($i),
                        'is_read' => true,
                    ]);
                }
            }
        }
    }

    private function getRandomMessage($index): string
    {
        $messages = [
            0 => [
                "Bonjour, je suis intéressé par cette expédition. Est-il possible de charger plus tôt ?",
                "Bonjour, quel serait votre meilleur prix pour cette livraison ?",
                "Bonjour, nous avons un camion disponible dans la zone le jour même.",
            ],
            1 => [
                "Bonjour, oui nous pouvons avancer l'heure si besoin. Quel créneau vous arrangerait ?",
                "Le prix affiché est notre budget maximum, mais nous pouvons discuter si vous garantissez une livraison express.",
                "C'est noté, l'adresse exacte est-elle accessible en semi-remorque ?",
            ],
            2 => [
                "Merci. 9h du matin serait parfait pour nous.",
                "Je comprends. Dans ce cas, je peux vous proposer 430€ tout compris.",
                "Oui, l'accès est dégagé, pas de soucis pour une semi.",
            ],
            3 => [
                "C'est entendu pour 9h. J'accepte votre offre.",
                "D'accord pour 430€. Veuillez mettre à jour la proposition officielle.",
                "Parfait, j'attends votre confirmation.",
            ]
        ];

        $pool = $messages[$index] ?? ["D'accord, merci."];
        return $pool[array_rand($pool)];
    }
}
