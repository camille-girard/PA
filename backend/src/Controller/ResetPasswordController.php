<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

#[Route('/api/reset-password')]
class ResetPasswordController extends AbstractController
{
    use ResetPasswordControllerTrait;

    public function __construct(
        private ResetPasswordHelperInterface $resetPasswordHelper,
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * Request a password reset via email.
     */
    #[Route('/request', name: 'api_forgot_password_request', methods: ['POST'])]
    public function request(Request $request, MailerInterface $mailer, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email'])) {
            return new JsonResponse([
                'error' => 'Email is required',
            ], Response::HTTP_BAD_REQUEST);
        }

        $email = $data['email'];

        $emailConstraint = new Assert\Email();
        $violations = $validator->validate($email, $emailConstraint);

        if (count($violations) > 0) {
            return new JsonResponse([
                'error' => 'Invalid email format',
            ], Response::HTTP_BAD_REQUEST);
        }

        $success = $this->processSendingPasswordResetEmail($email, $mailer);

        return new JsonResponse([
            'message' => 'If an account with that email exists, a password reset link has been sent.',
            'success' => true,
        ]);
    }

    /**
     * Validate a reset token.
     */
    #[Route('/validate-token/{token}', name: 'api_validate_reset_token', methods: ['GET'])]
    public function validateToken(string $token): JsonResponse
    {
        try {
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);

            return new JsonResponse([
                'valid' => true,
                'message' => 'Token is valid',
            ]);
        } catch (ResetPasswordExceptionInterface $e) {
            return new JsonResponse([
                'valid' => false,
                'error' => 'Invalid or expired token',
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Reset password with token and new password.
     */
    #[Route('/reset', name: 'api_reset_password', methods: ['POST'])]
    public function reset(Request $request, UserPasswordHasherInterface $passwordHasher, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['token']) || !isset($data['password'])) {
            return new JsonResponse([
                'error' => 'Token and password are required',
            ], Response::HTTP_BAD_REQUEST);
        }

        $token = $data['token'];
        $plainPassword = $data['password'];

        $passwordConstraints = [
            new Assert\NotBlank(),
            new Assert\Length(['min' => 8, 'minMessage' => 'Password must be at least 8 characters long']),
        ];

        $violations = $validator->validate($plainPassword, $passwordConstraints);

        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[] = $violation->getMessage();
            }

            return new JsonResponse([
                'error' => 'Password validation failed',
                'details' => $errors,
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            /** @var User $user */
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $e) {
            return new JsonResponse([
                'error' => 'Invalid or expired token',
            ], Response::HTTP_BAD_REQUEST);
        }

        // A password reset token should be used only once, remove it.
        $this->resetPasswordHelper->removeResetRequest($token);

        // Encode(hash) the plain password, and set it.
        $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));
        $this->entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'message' => 'Password has been successfully reset',
        ]);
    }

    private function processSendingPasswordResetEmail(string $emailFormData, MailerInterface $mailer): bool
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy([
            'email' => $emailFormData,
        ]);
        if (!$user) {
            error_log('Reset password attempt for non-existent email: '.$emailFormData);

            return false;
        }

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {
            error_log('Failed to generate reset token: '.$e->getMessage());

            return false;
        }

        $email = (new TemplatedEmail())
            ->from(new Address('mailer@popnbed.com', 'PopnBed'))
            ->to((string) $user->getEmail())
            ->subject('Réinitialisation de votre mot de passe PopnBed')
            ->htmlTemplate('reset_password/email.html.twig')
            ->context([
                'resetToken' => $resetToken,
                'frontendUrl' => $this->getParameter('frontend_url'),
            ])
        ;

        try {
            $mailer->send($email);
            error_log('Password reset email sent successfully to: '.$user->getEmail());

            return true;
        } catch (\Exception $e) {
            error_log('Failed to send password reset email: '.$e->getMessage());

            return false;
        } catch (TransportExceptionInterface $e) {
            error_log('Mailer transport error: '.$e->getMessage());

            return false;
        }
    }
}
