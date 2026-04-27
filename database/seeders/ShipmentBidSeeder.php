<?php

namespace Database\Seeders;

use App\Models\Shipment;
use App\Models\ShipmentBid;
use App\Models\BidMessage;
use App\Models\Review;
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
            // For each shipment, pick 2-5 random carriers to place bids
            $selectedCarriers = $carriers->random(rand(2, min(5, $carriers->count())));
            
            // Also include User #1 if they are an admin or carrier
            if (User::find(1) && !$selectedCarriers->pluck('id')->contains(1)) {
                $selectedCarriers->push(User::find(1));
            }

            foreach ($selectedCarriers as $carrier) {
                // Determine a random status for the bid
                $statuses = ['pending', 'rejected', 'countered'];
                $status = $statuses[array_rand($statuses)];

                $bid = ShipmentBid::updateOrCreate(
                    ['shipment_id' => $shipment->id, 'user_id' => $carrier->id],
                    [
                        'price' => rand(300, 1500),
                        'latest_pickup_date' => now()->addDays(rand(1, 5)),
                        'latest_pickup_time' => '09:00',
                        'latest_delivery_date' => now()->addDays(rand(6, 10)),
                        'latest_delivery_time' => '17:00',
                        'status' => $status,
                    ]
                );

                // Create a conversation for every bid
                $messageCount = rand(4, 8);
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

            // For some shipments, pick one bid to be 'accepted'
            if (rand(1, 10) <= 4) {
                $winningBid = $shipment->bids->random();
                $winningBid->update(['status' => 'accepted']);
                $shipment->bids()->where('id', '!=', $winningBid->id)->update(['status' => 'rejected']);
                
                // Randomly decide if the shipment is still active or already completed with a review
                if (rand(1, 2) === 1) {
                    $shipment->update(['status' => 'completed']);
                    
                    Review::create([
                        'shipment_id' => $shipment->id,
                        'reviewer_id' => $shipment->user_id,
                        'reviewee_id' => $winningBid->user_id,
                        'rating' => rand(4, 5),
                        'comment' => "Transporteur exceptionnel ! Livraison rapide et communication parfaite tout au long du trajet.",
                    ]);
                } else {
                    $shipment->update(['status' => 'active']);
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
