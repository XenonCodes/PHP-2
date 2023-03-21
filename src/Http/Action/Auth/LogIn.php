<?php

namespace XenonCodes\PHP2\Http\Action\Auth;

use DateTimeImmutable;
use XenonCodes\PHP2\Blog\AuthToken;
use XenonCodes\PHP2\Blog\Exceptions\AuthException;
use XenonCodes\PHP2\Blog\Repositories\AuthTokensRepository\AuthTokensRepositoryInterface;
use XenonCodes\PHP2\Http\Action\ActionInterface;
use XenonCodes\PHP2\Http\Auth\PasswordAuthenticationInterface;
use XenonCodes\PHP2\Http\ErrorResponse;
use XenonCodes\PHP2\Http\Request;
use XenonCodes\PHP2\Http\Response;
use XenonCodes\PHP2\Http\SuccessfulResponse;

class LogIn implements ActionInterface
{
    public function __construct(
        // Авторизация по паролю
        private PasswordAuthenticationInterface $passwordAuthentication,
        // Репозиторий токенов
        private AuthTokensRepositoryInterface $authTokensRepository
    ) {
    }
    public function handle(Request $request): Response
    {
        // Аутентифицируем пользователя
        try {
            $user = $this->passwordAuthentication->user($request);
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }

        // Генерируем токен
        $authToken = new AuthToken(
            // Случайная строка длиной 40 символов
            bin2hex(random_bytes(40)),
            $user->getId(),
            // Срок годности - 1 день
            (new DateTimeImmutable())->modify('+1 day')
        );

        // Сохраняем токен в репозиторий
        $this->authTokensRepository->save($authToken);
        
        // Возвращаем токен
        return new SuccessfulResponse([
            'token' => $authToken->token(),
        ]);
    }
}
