<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Repository\AccommodationRepository;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Stripe\Webhook;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class StripeWebhookController extends AbstractController
{
    #[Route('/api/stripe/webhook', name: 'stripe_webhook', methods: ['POST'])]
    public function __invoke(
        Request $request,
        EntityManagerInterface $em,
        ClientRepository $clientRepository,
        AccommodationRepository $accommodationRepository,
        LoggerInterface $logger
    ): Response {
        $payload = $request->getContent();
        $sigHeader = $request->headers->get('stripe-signature');
        $endpointSecret = $_ENV['STRIPE_WEBHOOK_SECRET'];

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (\Exception $e) {
            $logger->error('❌ Erreur de Webhook Stripe : ' . $e->getMessage());
            return new Response('Webhook error: ' . $e->getMessage(), 400);
        }

        // Log basique dans un fichier si besoin
        file_put_contents('stripe_webhook.log', '✅ Webhook reçu : ' . $event->type . " à " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
        $logger->info('✅ Webhook Stripe reçu : ' . $event->type);

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;

            $client = $clientRepository->find($session->metadata->client_id ?? null);
            $accommodation = $accommodationRepository->find($session->metadata->accommodation_id ?? null);

            if ($client && $accommodation) {
                $booking = new Booking();
                $booking->setClient($client);
                $booking->setAccommodation($accommodation);
                $booking->setStartDate(new \DateTime($session->metadata->start_date));
                $booking->setEndDate(new \DateTime($session->metadata->end_date));
                $booking->setTotalPrice($session->metadata->total_price);
                $booking->setStatus('accepted');

                $em->persist($booking);
                $em->flush();

                $logger->info('✅ Réservation enregistrée en BDD pour client #' . $client->getId());
                file_put_contents('stripe_webhook.log', '✅ Réservation créée pour client #' . $client->getId() . "\n", FILE_APPEND);
            } else {
                $logger->warning('⚠️ Données manquantes pour créer une réservation.');
            }
        }

        return new Response('OK', 200);
    }
}
