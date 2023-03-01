<?php

namespace XenonCodes\PHP2\Blog\Repositories\UsersRepository;

use XenonCodes\PHP2\Blog\User;
use XenonCodes\PHP2\Blog\UUID;

interface UsersRepositoryInterface
{
    public function save(User $user): void;
    public function get(UUID $uuid): User;
    public function getByLogin(string $login): User;
}
