<?php

namespace XenonCodes\PHP2\Blog;

use XenonCodes\PHP2\Person\Name;
use DateTimeImmutable;

class User
{
    /**
     * @param UUID $id UUID пользователя
     * @param Name $userName Имя и Фамилия
     * @param string $login логин пользователя
     * @param DateTimeImmutable $registeredOn дата создания объекта User
     */
    public function __construct(
        private UUID $id,
        private Name $userName,
        private string $login,
        private DateTimeImmutable $registeredOn
    ) {
    }

    public function __toString()
    {
        return "Пользователь $this->userName c ID: $this->id и логином $this->login (на сайте с " . $this->getRegistredOn() . ")";
    }

    public function getId(): UUID
    {
        return $this->id;
    }

    public function getName(): Name
    {
        return $this->userName;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function getRegistredOn(): string
    {
        return $this->registeredOn->format('Y-m-d');
    }
}
