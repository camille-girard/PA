<?php

namespace App\EventListener;

use App\Exception\TwoFactorRequiredException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ExceptionListener
{
    public function __construct(
        private LoggerInterface $loggerInterface,
    ) {
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof TwoFactorRequiredException) {
            $this->loggerInterface->info('Two Factor Required Exception caught');

            $response = new JsonResponse([
                'requiresTwoFactor' => true,
                'message' => $exception->getMessage(),
                'email' => $exception->getEmail(),
            ], Response::HTTP_OK);

            $event->setResponse($response);
        }
    }
}
