<?php

// namespace src\Person;
namespace XenonCodes\PHP2\Person;

class Name
{
    /**
     * @param string $firstName Имя пользователя
     * @param string $lastName Фамилия пользователя
     */
    public function __construct(
        private string $firstName,
        private string $lastName
    ) {
    }

    public function __toString()
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }
}
