<?php

namespace XenonCodes\PHP2\Blog\Commands;

use DateTimeImmutable;
use XenonCodes\PHP2\Blog\Exceptions\CommandException;
use XenonCodes\PHP2\Blog\Exceptions\UserNotFoundException;
use XenonCodes\PHP2\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use XenonCodes\PHP2\Blog\User;
use XenonCodes\PHP2\Blog\UUID;
use XenonCodes\PHP2\Person\Name;

class CreateUserCommand
{
    // Команда зависит от контракта репозитория пользователей,
    // а не от конкретной реализации
    public function __construct(
        private UsersRepositoryInterface $usersRepository
    ) {
    }

    public function handle(Arguments $argument): void
    {
        $login = $argument->get('login');
        
        // Проверяем, существует ли пользователь в репозитории
        if ($this->userExists($login)) {
            // Бросаем исключение, если пользователь уже существует
            throw new CommandException("User already exists: $login");
        }
        
        // Сохраняем пользователя в репозиторий
        $this->usersRepository->save(new User(
            UUID::random(),
            new Name(
                $argument->get('first_name'),
                $argument->get('last_name')
            ),
            $login,
            new DateTimeImmutable()
        ));
    }

    private function userExists(string $login): bool
    {
        try {
            // Пытаемся получить пользователя из репозитория
            $this->usersRepository->getByLogin($login);
        } catch (UserNotFoundException) {
            return false;
        }
        return true;
    }
}
