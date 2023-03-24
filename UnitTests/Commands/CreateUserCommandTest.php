<?php

namespace XenonCodes\PHP2\Tests\Commands;

use DateTimeImmutable;
use XenonCodes\PHP2\Blog\Commands\Arguments;
use XenonCodes\PHP2\Blog\Exceptions\CommandException;
use XenonCodes\PHP2\Blog\Commands\CreateUserCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use XenonCodes\PHP2\Blog\Commands\Users\CreateUser;
use XenonCodes\PHP2\Blog\Exceptions\ArgumentsException;
use XenonCodes\PHP2\Blog\Exceptions\UserNotFoundException;
use XenonCodes\PHP2\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use XenonCodes\PHP2\Blog\User;
use XenonCodes\PHP2\Blog\UUID;
use XenonCodes\PHP2\Person\Name;
use XenonCodes\PHP2\Tests\DummyLogger;

class CreateUserCommandTest extends TestCase
{
    // Проверяем, что команда создания пользователя бросает исключение,
    // если пользователь с таким именем уже существует
    public function testItThrowsAnExceptionWhenUserAlreadyExists(): void
    {
        // Создаём объект команды
        // У команды одна зависимость - UsersRepositoryInterface
        $command = new CreateUserCommand(
            $usersRepository = new class implements UsersRepositoryInterface  // здесь должна быть реализация UsersRepositoryInterfacew
            {
                public function save(User $user): void
                {
                    // Ничего не делаем
                }
                public function get(UUID $uuid): User
                {
                    // И здесь ничего не делаем
                    throw new UserNotFoundException("Not found");
                }
                public function getByLogin(string $login): User
                {
                    // Нас интересует реализация только этого метода
                    // Для нашего теста не важно, что это будет за пользователь,
                    // поэтому возвращаем совершенно произвольного
                    return new User(UUID::random(), new Name("first", "last"), "user123", "123", new DateTimeImmutable());
                }
                public function checkUser(string $login): void
                {
                    // Ничего не делаем
                }
            },
            // Тестовая реализация логгера
            new DummyLogger()
        );
        // Описываем тип ожидаемого исключения
        $this->expectException(CommandException::class);
        // и его сообщение
        $this->expectExceptionMessage('User already exists: Ivan');
        // Запускаем команду с аргументами
        $command->handle(new Arguments([
            'login' => 'Ivan',
            'password' => '123'
        ]));
    }

    public function testItRequiresPassword(): void
    {
        $command = new CreateUser(
            $this->makeUsersRepository(),
        );

        $this->expectException(RuntimeException::class);

        $this->expectExceptionMessage(
            'Not enough arguments (missing: "password").'
        );

        $command->run(
            new ArrayInput([
                'login' => 'Ivan',
                'first_name' => 'Ivan',
                'last_name' => 'Petrov',
            ]),
            new NullOutput()
        );
    }

    // Тест проверяет, что команда действительно требует имя пользователя
    public function testItRequiresFirstName(): void
    {
        $command = new CreateUser(
            $this->makeUsersRepository(),
        );

        $this->expectException(RuntimeException::class);

        $this->expectExceptionMessage(
            'Not enough arguments (missing: "first_name").'
        );

        $command->run(
            new ArrayInput([
                'login' => 'Ivan',
                'password' => 'some_password',
                'last_name' => 'Petrov',
            ]),
            new NullOutput()
        );
    }

    // Тест проверяет, что команда действительно требует фамилию пользователя
    public function testItRequiresLastName(): void
    {
        // Тестируем новую команду
        $command = new CreateUser(
            $this->makeUsersRepository(),
        );
        // Меняем тип ожидаемого исключения ..
        $this->expectException(RuntimeException::class);
        // .. и его сообщение
        $this->expectExceptionMessage(
            'Not enough arguments (missing: "last_name").'
        );
        // Запускаем команду методом run вместо handle
        $command->run(
            // Передаём аргументы как ArrayInput,
            // а не Arguments
            // Сами аргументы не меняются
            new ArrayInput([
                'login' => 'Ivan',
                'password' => 'some_password',
                'first_name' => 'Ivan',
            ]),
            // Передаём также объект,
            // реализующий контракт OutputInterface
            // Нам подойдёт реализация,
            // которая ничего не делает
            new NullOutput()
        );
    }

    // Тест, проверяющий, что команда сохраняет пользователя в репозитории
    public function testItSavesUserToRepository(): void
    {
        // Создаём объект анонимного класса
        $usersRepository = new class implements UsersRepositoryInterface
        {
            // В этом свойстве мы храним информацию о том,
            // был ли вызван метод save
            private bool $called = false;
            public function save(User $user): void
            {
                // Запоминаем, что метод save был вызван
                $this->called = true;
            }
            public function get(UUID $uuid): User
            {
                throw new UserNotFoundException("Not found");
            }
            public function getByLogin(string $login): User
            {
                throw new UserNotFoundException("Not found");
            }
            public function checkUser(string $login): void
            {
                // Ничего не делаем
            }
            // Этого метода нет в контракте UsersRepositoryInterface,
            // но ничто не мешает его добавить.
            // С помощью этого метода мы можем узнать,
            // был ли вызван метод save
            public function wasCalled(): bool
            {
                return $this->called;
            }
        };
        $command = new CreateUser(
            $usersRepository
        );
        $command->run(
            new ArrayInput([
                'login' => 'Ivan',
                'password' => 'some_password',
                'first_name' => 'Ivan',
                'last_name' => 'Nikitin',
            ]),
            new NullOutput()
        );
        $this->assertTrue($usersRepository->wasCalled());
    }

    private function makeUsersRepository(): UsersRepositoryInterface
    {
        return new class implements UsersRepositoryInterface //- это объект анонимного класса, реализующего контракт UsersRepositoryInterface
        {
            public function save(User $user): void
            {
                // Ничего не делаем
            }
            public function get(UUID $uuid): User
            {
                // И здесь ничего не делаем
                throw new UserNotFoundException("Not found");
            }
            public function getBylogin(string $login): User
            {
                // И здесь ничего не делаем
                throw new UserNotFoundException("Not found");
            }
            public function checkUser(string $login): void
            {
                // Ничего не делаем
            }
        };
    }
}
