<?php

namespace XenonCodes\PHP2\Blog;

use DateTimeImmutable;

class AuthToken
{
    public function __construct(
        // Строка токена
        private string $token,
        // UUID пользователя
        private UUID $userUuid,
        // Срок годности
        private DateTimeImmutable $expiresOn
    ) {
    }

    public function token(): string
    {
        return $this->token;
    }

    public function getUserUuid(): UUID
    {
        return $this->userUuid;
    }

    public function expiresOn(): DateTimeImmutable
    {
        return $this->expiresOn;
    }

    public function setExpiresOn(DateTimeImmutable $new): void
    {
        $this->expiresOn = $new;
    }
}
