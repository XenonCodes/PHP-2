<?php

namespace XenonCodes\PHP2\Blog\Repositories\AuthTokensRepository;

use XenonCodes\PHP2\Blog\AuthToken;

interface AuthTokensRepositoryInterface
{
    // Метод сохранения токена
    public function save(AuthToken $authToken): void;
    // Метод получения токена
    public function get(string $token): AuthToken;
}
