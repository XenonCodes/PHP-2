<?php

namespace XenonCodes\PHP2\Blog\Repositories\UsersRepository;

use XenonCodes\PHP2\Blog\Exceptions\CheckingDuplicateLoginException;
use XenonCodes\PHP2\Blog\User;
use XenonCodes\PHP2\Blog\Exceptions\UserNotFoundException;
use XenonCodes\PHP2\Blog\UUID;

class InMemoryUsersRepository implements UsersRepositoryInterface
{
    private array $users = [];

    /**
     * @param User $user Сохраняем пользователя в массив
     */
    public function save(User $user): void
    {
        $this->users[] = $user;
    }

    /**
     * @param UUID $id
     * @return User
     * @throws UserNotFoundException
     */
    public function get(UUID $id): User
    {
        foreach ($this->users as $user) {
            if ($user->getId() === $id) {
                return $user;
            }
        }
        throw new UserNotFoundException("Пользователь c ID: $id найден.");
    }

    /**
     * @param string $login Логин пользователя
     * @return User
     * @throws UserNotFoundException
     */
    public function getByLogin(string $login): User
    {
        foreach ($this->users as $user) {
            if ($user->getLogin === $login) {
                return $user;
            }
        }
        throw new UserNotFoundException("Пользователь $login не найден");
    }

    public function checkUser(string $login): void
    {
        foreach ($this->users as $user) {
            if ($user->getLogin === $login) {
                throw new CheckingDuplicateLoginException(
                    'Попробуйте другой логин для регистрации.'
                );
            }
        }
    }
}
