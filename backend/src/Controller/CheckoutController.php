<?php

namespace App\Controller;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/checkout', name: 'api_checkout_')]
class CheckoutController extends AbstractController
{
    #[Route('/create-session', name: 'create_session', methods: ['POST'])]
    public function createCheckoutSession(Request $request): JsonResponse
    {
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        $data = json_decode($request->getContent(), true);

        $price = $data['totalPrice']; // en euros
        $clientId = $data['clientId'];
        $accommodationId = $data['accommodationId'];
        $startDate = $data['startDate'];
        $endDate = $data['endDate'];

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => 'Réservation PopnBed',
                    ],
                    'unit_amount' => $price * 100, // Stripe attend des centimes
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => 'http://localhost:8085/booking/success',
            'cancel_url' => 'http://localhost:8085/booking/cancel',
            'metadata' => [
                'client_id' => $clientId,
                'accommodation_id' => $accommodationId,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'total_price' => $price,
            ],
        ]);

        return $this->json(['id' => $session->id]);
    }
}
