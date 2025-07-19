<?php

namespace App\Service;

use App\Entity\Booking;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class BookingEmailService
{
    public function __construct(
        private MailerInterface $mailer,
        private Environment $twig,
        private LoggerInterface $logger,
        private string $fromEmail = 'noreply@popnbed.com',
    ) {
    }

    public function sendBookingConfirmationToClient(Booking $booking): void
    {
        try {
            $client = $booking->getClient();
            $accommodation = $booking->getAccommodation();

            if (!$client || !$accommodation) {
                $this->logger->warning('Impossible d\'envoyer l\'email de confirmation : client ou hébergement manquant');

                return;
            }

            $email = (new Email())
                ->from($this->fromEmail)
                ->to($client->getEmail())
                ->subject('Confirmation de votre réservation PopnBed')
                ->html($this->twig->render('emails/booking_confirmation_client.html.twig', [
                    'booking' => $booking,
                    'client' => $client,
                    'accommodation' => $accommodation,
                ]));

            $this->mailer->send($email);
            $this->logger->info("Email de confirmation envoyé au client {$client->getEmail()} pour la réservation #{$booking->getId()}");
        } catch (\Throwable $e) {
            $this->logger->error("Erreur lors de l'envoi de l'email de confirmation au client : {$e->getMessage()}");
        }
    }

    public function sendBookingNotificationToOwner(Booking $booking): void
    {
        try {
            $accommodation = $booking->getAccommodation();
            $client = $booking->getClient();

            if (!$accommodation || !$client) {
                $this->logger->warning('Impossible d\'envoyer l\'email de notification : hébergement ou client manquant');

                return;
            }

            $owner = $accommodation->getOwner();
            if (!$owner) {
                $this->logger->warning('Impossible d\'envoyer l\'email de notification : propriétaire manquant');

                return;
            }

            $email = (new Email())
                ->from($this->fromEmail)
                ->to($owner->getEmail())
                ->subject('Nouvelle réservation pour votre hébergement')
                ->html($this->twig->render('emails/booking_notification_owner.html.twig', [
                    'booking' => $booking,
                    'client' => $client,
                    'accommodation' => $accommodation,
                    'owner' => $owner,
                ]));

            $this->mailer->send($email);
            $this->logger->info("Email de notification envoyé au propriétaire {$owner->getEmail()} pour la réservation #{$booking->getId()}");
        } catch (\Throwable $e) {
            $this->logger->error("Erreur lors de l'envoi de l'email de notification au propriétaire : {$e->getMessage()}");
        }
    }

    public function sendBookingStatusUpdate(Booking $booking, string $previousStatus): void
    {
        try {
            $client = $booking->getClient();

            if (!$client) {
                $this->logger->warning('Impossible d\'envoyer l\'email de mise à jour : client manquant');

                return;
            }

            $email = (new Email())
                ->from($this->fromEmail)
                ->to($client->getEmail())
                ->subject('Mise à jour de votre réservation PopnBed')
                ->html($this->twig->render('emails/booking_status_update.html.twig', [
                    'booking' => $booking,
                    'client' => $client,
                    'accommodation' => $booking->getAccommodation(),
                    'previousStatus' => $previousStatus,
                ]));

            $this->mailer->send($email);
            $this->logger->info("Email de mise à jour envoyé au client {$client->getEmail()} pour la réservation #{$booking->getId()}");
        } catch (\Throwable $e) {
            $this->logger->error("Erreur lors de l'envoi de l'email de mise à jour : {$e->getMessage()}");
        }
    }

    public function sendBookingCancellationToClient(Booking $booking): void
    {
        try {
            $client = $booking->getClient();
            $accommodation = $booking->getAccommodation();

            if (!$client || !$accommodation) {
                $this->logger->warning('Impossible d\'envoyer l\'email d\'annulation : client ou hébergement manquant');

                return;
            }

            $email = (new Email())
                ->from($this->fromEmail)
                ->to($client->getEmail())
                ->subject('Annulation de votre réservation PopnBed')
                ->html($this->twig->render('emails/booking_cancellation_client.html.twig', [
                    'booking' => $booking,
                    'client' => $client,
                    'accommodation' => $accommodation,
                ]));

            $this->mailer->send($email);
            $this->logger->info("Email d'annulation envoyé au client {$client->getEmail()} pour la réservation #{$booking->getId()}");
        } catch (\Throwable $e) {
            $this->logger->error("Erreur lors de l'envoi de l'email d'annulation au client : {$e->getMessage()}");
        }
    }

    public function sendBookingCancellationToOwner(Booking $booking): void
    {
        try {
            $accommodation = $booking->getAccommodation();
            $client = $booking->getClient();

            if (!$accommodation || !$client) {
                $this->logger->warning('Impossible d\'envoyer l\'email d\'annulation : hébergement ou client manquant');

                return;
            }

            $owner = $accommodation->getOwner();
            if (!$owner) {
                $this->logger->warning('Impossible d\'envoyer l\'email d\'annulation : propriétaire manquant');

                return;
            }

            $email = (new Email())
                ->from($this->fromEmail)
                ->to($owner->getEmail())
                ->subject('Annulation d\'une réservation pour votre hébergement')
                ->html($this->twig->render('emails/booking_cancellation_owner.html.twig', [
                    'booking' => $booking,
                    'client' => $client,
                    'accommodation' => $accommodation,
                    'owner' => $owner,
                ]));

            $this->mailer->send($email);
            $this->logger->info("Email d'annulation envoyé au propriétaire {$owner->getEmail()} pour la réservation #{$booking->getId()}");
        } catch (\Throwable $e) {
            $this->logger->error("Erreur lors de l'envoi de l'email d'annulation au propriétaire : {$e->getMessage()}");
        }
    }
}
