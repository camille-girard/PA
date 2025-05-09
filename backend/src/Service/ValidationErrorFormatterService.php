<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationErrorFormatterService
{
    /**
     * @return array<string, string>
     */
    public function formatErrors(ConstraintViolationListInterface $errors): array
    {
        $errorMessages = [];
        foreach ($errors as $error) {
            $errorMessages[$error->getPropertyPath()] = $error->getMessage();
        }

        return $errorMessages;
    }

    public function createValidationErrorResponse(ConstraintViolationListInterface $errors): JsonResponse
    {
        $errorMessages = $this->formatErrors($errors);

        return new JsonResponse(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
    }
}
