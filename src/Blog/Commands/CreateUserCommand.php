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

    public function handle(Arguments $arguments): void
    {
        // Логируем информацию о том, что команда запущена
        // Уровень логирования – INFO
        $this->logger->info("Create user command started");

        $login = $arguments->get('login');

        // Проверяем, существует ли пользователь в репозитории
        if ($this->userExists($login)) {
            // Логируем сообщение с уровнем WARNING
            $this->logger->warning("User already exists: $login");

            // Бросаем исключение, если пользователь уже существует
            throw new CommandException("User already exists: $login");
        }

        // Создаём объект пользователя
        // Функция createFrom сама создаст UUID
        // и захеширует пароль
        $user = User::createFrom(
            $login,
            $arguments->get('password'),
            new Name(
                $arguments->get('first_name'),
                $arguments->get('last_name')
            )
        );

        // Сохраняем пользователя в репозиторий
        $this->usersRepository->save($user);

        // Логируем информацию о новом пользователе
        $this->logger->info("User created: " . $user->getId());
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
