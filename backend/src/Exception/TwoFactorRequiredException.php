<?php

namespace App\Exception;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

class TwoFactorRequiredException extends AuthenticationException
{
    private string $email;

    public function __construct(string $email, string $message = '2FA verification required')
    {
        parent::__construct($message);
        $this->email = $email;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
