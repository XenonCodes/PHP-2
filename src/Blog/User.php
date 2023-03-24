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
     * @param string $hashedPassword пароль пользователя
     * @param DateTimeImmutable $registeredOn дата создания объекта User
     */
    public function __construct(
        private UUID $id,
        private Name $userName,
        private string $login,
        private string $hashedPassword,
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

    public function getHashedPassword(): string
    {
        return $this->hashedPassword;
    }

    // Функция для вычисления хеша
    private static function hash(string $password, UUID $uuid): string
    {
        // Используем UUID в качестве соли
        return hash('sha256', $uuid . $password);
    }

    // Функция для проверки предъявленного пароля
    public function checkPassword(string $password): bool
    {
        // Передаём UUID пользователя
        // в функцию хеширования пароля
        return $this->hashedPassword === self::hash($password, $this->getId());
    }

    // Функция для создания нового пользователя
    public static function createFrom(
        string $login,
        string $password,
        Name $name
    ): self {
        // Генерируем UUID
        $uuid = UUID::random();
        return new self(
            $uuid,
            $name,
            $login,
            self::hash($password, $uuid),
            new DateTimeImmutable()
        );
    }

    public function getRegistredOn(): string
    {
        return $this->registeredOn->format('Y-m-d');
    }
}
