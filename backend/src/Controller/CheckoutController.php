<?php

namespace App\Controller;

use Stripe\Checkout\Session;
use Stripe\Stripe;
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
        $price = (float) $data['totalPrice'];      // euros
        $clientId = (int) $data['clientId'];
        $accommodationId = (int) $data['accommodationId'];
        $startDate = $data['startDate'];
        $endDate = $data['endDate'];

        $baseUrl = $_ENV['FRONT_BASE_URL'] ?? 'http://localhost:8085';

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => ['name' => 'Réservation PopnBed'],
                    'unit_amount' => (int) round($price * 100), // centimes
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $baseUrl.'/booking/success?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $baseUrl.'/booking/cancel',
            'metadata' => [
                'client_id' => (string) $clientId,
                'accommodation_id' => (string) $accommodationId,
                'start_date' => (string) $startDate,
                'end_date' => (string) $endDate,
                'total_price' => (string) $price,
            ],
        ]);

        return $this->json(['id' => $session->id]);
    }
}
