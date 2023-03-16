<?php

namespace XenonCodes\PHP2\Tests\Repositories;

use DateTimeImmutable;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use XenonCodes\PHP2\Blog\Exceptions\UserNotFoundException;
use XenonCodes\PHP2\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use XenonCodes\PHP2\Blog\User;
use XenonCodes\PHP2\Blog\UUID;
use XenonCodes\PHP2\Person\Name;
use XenonCodes\PHP2\Tests\DummyLogger;

class SqliteUsersRepositoryTest extends TestCase
{
    public function testItThrowsAnExceptionWhenUserNotFound(): void
    {
        $connectionMock = $this->createStub(PDO::class);
        $statementStub = $this->createStub(PDOStatement::class);

        $statementStub->method('fetch')->willReturn(false);
        $connectionMock->method('prepare')->willReturn($statementStub);

        $repository = new SqliteUsersRepository($connectionMock, new DummyLogger());

        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage('Ivan не найден.');

        $repository->getByLogin('Ivan');
    }

    //Тест, проверяющий, что репозиторий сохраняет данные в БД
    public function testItSavesUserToDatabase(): void
    {
        // 2. Создаём стаб подключения
        $connectionStub = $this->createStub(PDO::class);
        // 4. Создаём мок запроса, возвращаемый стабом подключения
        $statementMock = $this->createMock(PDOStatement::class);
        // 5. Описываем ожидаемое взаимодействие
        // нашего репозитория с моком запроса
        $statementMock
            ->expects($this->once()) // Ожидаем, что будет вызван один раз
            ->method('execute') // метод execute
            ->with([ // с единственным аргументом - массивом
                ':uuid' => '123e4567-e89b-12d3-a456-426614174000',
                ':login' => 'ivan123',
                ':first_name' => 'Ivan',
                ':last_name' => 'Nikitin',
                ':date_register' => '2023-03-05',
            ]);
        // 3. При вызове метода prepare стаб подключения
        // возвращает мок запроса
        $connectionStub->method('prepare')->willReturn($statementMock);
        // 1. Передаём в репозиторий стаб подключения
        $repository = new SqliteUsersRepository($connectionStub, new DummyLogger());
        // Вызываем метод сохранения пользователя
        $repository->save(
            new User( // Свойства пользователя точно такие,
                // как и в описании мока
                new UUID('123e4567-e89b-12d3-a456-426614174000'),
                new Name('Ivan', 'Nikitin'),
                'ivan123',
                new DateTimeImmutable('2023-03-05')
            )
        );
    }
}
