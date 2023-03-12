<?php

namespace XenonCodes\PHP2\Blog\Repositories\UsersRepository;

use DateTimeImmutable;
use PDO;
use PDOStatement;
use XenonCodes\PHP2\Blog\Exceptions\CheckingDuplicateLoginException;
use XenonCodes\PHP2\Blog\Exceptions\UserNotFoundException;
use XenonCodes\PHP2\Blog\User;
use XenonCodes\PHP2\Blog\UUID;
use XenonCodes\PHP2\Person\Name;

class SqliteUsersRepository implements UsersRepositoryInterface
{
    public function __construct(
        private PDO $connection
    ) {
    }

    public function save(User $user): void
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM users WHERE login = :login'
        );
    
        $statement->execute([
            ':login' => $user->getLogin(),
        ]);
    
        $result = $statement->fetch(PDO::FETCH_ASSOC);
    
        if ($result) {
            throw new CheckingDuplicateLoginException('Попробуйте другой логин для регистрации.');
        }

        $statement = $this->connection->prepare(
            'INSERT INTO users (uuid, login, first_name, last_name, date_register)
            VALUES (:uuid, :login, :first_name, :last_name, :date_register)'
        );

        // Выполняем запрос с конкретными значениями
        $statement->execute([
            ':uuid' => $user->getId(),
            ':login' => $user->getLogin(),
            ':first_name' => $user->getName()->getFirstName(),
            ':last_name' => $user->getName()->getLastName(),
            ':date_register' => $user->getRegistredOn(),
        ]);
    }

    public function get(UUID $uuid): User
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM users WHERE uuid = :uuid'
        );
        $statement->execute([
            ':uuid' => (string)$uuid,
        ]);

        return $this->getUser($statement, $uuid);
    }

    /**
     * @param string $login Логин пользователя
     * @return User
     * @throws UserNotFoundException
     */
    public function getByLogin(string $login): User
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM users WHERE login = :login'
        );
        $statement->execute([
            ':login' => $login
        ]);

        return $this->getUser($statement, $login);
    }

    /**
     * @throws UserNotFoundException
     * @throws InvalidArgumentException
     */
    private function getUser(PDOStatement $statement, string $user): User
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if ($result === false) {
            throw new UserNotFoundException(
                "$user не найден."
            );
        }
        // Создаём объект пользователя с полем username
        return new User(
            new UUID($result['uuid']),
            new Name($result['first_name'], $result['last_name']),
            $result['login'],
            new DateTimeImmutable($result['date_register']),
        );
    }
}
