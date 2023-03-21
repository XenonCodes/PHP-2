<?php

namespace XenonCodes\PHP2\Http\Auth;

use XenonCodes\PHP2\Blog\Exceptions\AuthException;
use XenonCodes\PHP2\Blog\Exceptions\HttpException;
use XenonCodes\PHP2\Blog\Exceptions\UserNotFoundException;
use XenonCodes\PHP2\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use XenonCodes\PHP2\Blog\User;
use XenonCodes\PHP2\Http\Request;

class PasswordAuthentication implements PasswordAuthenticationInterface
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository
    ) {
    }

    public function user(Request $request): User
    {
        // 1. Идентифицируем пользователя
        try {
            $login = $request->jsonBodyField('login');
        } catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }

        try {
            $user = $this->usersRepository->getByLogin($login);
        } catch (UserNotFoundException $e) {
            throw new AuthException($e->getMessage());
        }

        // 2. Аутентифицируем пользователя
        // Проверяем, что предъявленный пароль
        // соответствует сохранённому в БД
        try {
            $password = $request->jsonBodyField('password');
        } catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }

        if (!$user->checkPassword($password)) {
            // Если пароли не совпадают — бросаем исключение
            throw new AuthException('Неверный пароль');
        }

        // Пользователь аутентифицирован
        return $user;
    }
}
