<?php

// namespace src\Blog\Repositories\UsersRepository;

// use src\Blog\User;
// use src\Blog\Exceptions\UserNotFoundException;

namespace XenonCodes\PHP2\Blog\Repositories\UsersRepository;

use XenonCodes\PHP2\Blog\User;
use XenonCodes\PHP2\Blog\Exceptions\UserNotFoundException;

class InMemoryUsersRepository
{
    private array $users = [];
    
    public function save(User $user): void
    {
        $this->users[] = $user;
    }
    /**
     * @param int $id
     * @return User
     * @throws UserNotFoundException
     */
    public function get(int $id): User
    {
        foreach ($this->users as $user) {
            if ($user->getId() === $id) {
                return $user;
            }
        }
        throw new UserNotFoundException("Пользователь не найден: $id");
    }
}
