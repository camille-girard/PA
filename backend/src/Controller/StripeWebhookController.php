<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Enum\BookingStatus;
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
        ClientRepository $clientRepo,
        AccommodationRepository $accommodationRepo,
        LoggerInterface $logger,
    ): Response {
        $payload = $request->getContent();
        $sigHeader = $request->headers->get('stripe-signature');
        $secret = $_ENV['STRIPE_WEBHOOK_SECRET'] ?? null;

        // 🔍 Logs de debug
        $logger->info('🔑 Secret configuré : ' . ($secret ? 'OUI (' . substr($secret, 0, 15) . '...)' : 'NON'));
        $logger->info('📝 Signature reçue : ' . ($sigHeader ? substr($sigHeader, 0, 30) . '...' : 'MANQUANTE'));
        $logger->info('📦 Payload length : ' . strlen($payload));

        if (!$secret) {
            $logger->error('STRIPE_WEBHOOK_SECRET manquant dans .env');
            return new Response('Configuration error', 500);
        }

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
            $logger->info('✅ Signature validée avec succès pour événement : ' . $event->type);
        } catch (\Throwable $e) {
            $logger->error('❌ Stripe Webhook Signature ERROR: ' . $e->getMessage());
            $logger->error('❌ Secret utilisé : ' . substr($secret, 0, 15) . '...');
            return new Response('Invalid signature', 400);
        }

        $logger->info('Stripe Webhook reçu : ' . $event->type);

        if ('checkout.session.completed' === $event->type) {
            try {
                $session = $event->data->object;

                $logger->info('📦 Données session : ' . json_encode($session));

                $clientId = $session->metadata->client_id ?? null;
                $accommodationId = $session->metadata->accommodation_id ?? null;
                $startDate = $session->metadata->start_date ?? null;
                $endDate = $session->metadata->end_date ?? null;
                $totalPrice = (float) ($session->metadata->total_price ?? 0);

                if ($clientId && $accommodationId && $startDate && $endDate) {
                    $client = $clientRepo->find($clientId);
                    $accommodation = $accommodationRepo->find($accommodationId);

                    if ($client && $accommodation) {
                        $booking = new Booking();
                        $booking
                            ->setClient($client)
                            ->setAccommodation($accommodation)
                            ->setStartDate(new \DateTimeImmutable($startDate))
                            ->setEndDate(new \DateTimeImmutable($endDate))
                            ->setTotalPrice($totalPrice)
                            ->setStatus(BookingStatus::ACCEPTED)
                            ->setCreatedAt(new \DateTimeImmutable());

                        $em->persist($booking);
                        $em->flush();

                        $logger->info("Réservation enregistrée : ID #{$booking->getId()}");
                    } else {
                        $logger->warning("Client ou hébergement introuvable : client #$clientId / acc #$accommodationId");
                    }
                } else {
                    $logger->warning('Données metadata manquantes dans la session.');
                }
            } catch (\Throwable $e) {
                $logger->error('Erreur traitement checkout.session.completed : ' . $e->getMessage());
                return new Response('Erreur serveur', 500);
            }
        }

        return new Response('OK', 200);
    }
}