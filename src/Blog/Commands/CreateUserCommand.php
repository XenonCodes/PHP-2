<?php

namespace XenonCodes\PHP2\Blog\Commands;

use DateTimeImmutable;
use XenonCodes\PHP2\Blog\Exceptions\CommandException;
use XenonCodes\PHP2\Blog\Exceptions\UserNotFoundException;
use XenonCodes\PHP2\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use XenonCodes\PHP2\Blog\User;
use XenonCodes\PHP2\Blog\UUID;
use XenonCodes\PHP2\Person\Name;
use Psr\Log\LoggerInterface;

class CreateUserCommand
{
    // Команда зависит от контракта репозитория пользователей,
    // а не от конкретной реализации
    public function __construct(
        private UsersRepositoryInterface $usersRepository,
        private LoggerInterface $logger,
    ) {
    }

    public function handle(Arguments $argument): void
    {
        // Логируем информацию о том, что команда запущена
        // Уровень логирования – INFO
        $this->logger->info("Create user command started");

        $login = $argument->get('login');

        // Проверяем, существует ли пользователь в репозитории
        if ($this->userExists($login)) {
            // Логируем сообщение с уровнем WARNING
            $this->logger->warning("User already exists: $login");

            // Бросаем исключение, если пользователь уже существует
            throw new CommandException("User already exists: $login");
        }

        $uuid = UUID::random();

        // Сохраняем пользователя в репозиторий
        $this->usersRepository->save(new User(
            $uuid,
            new Name(
                $argument->get('first_name'),
                $argument->get('last_name')
            ),
            $login,
            new DateTimeImmutable()
        ));

        // Логируем информацию о новом пользователе
        $this->logger->info("User created: $uuid");
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
