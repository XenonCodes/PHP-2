<?php

namespace XenonCodes\PHP2\Tests\Commands;

use DateTimeImmutable;
use XenonCodes\PHP2\Blog\Commands\Arguments;
use XenonCodes\PHP2\Blog\Exceptions\CommandException;
use XenonCodes\PHP2\Blog\Commands\CreateUserCommand;
use PHPUnit\Framework\TestCase;
use XenonCodes\PHP2\Blog\Exceptions\ArgumentsException;
use XenonCodes\PHP2\Blog\Exceptions\UserNotFoundException;
use XenonCodes\PHP2\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use XenonCodes\PHP2\Blog\User;
use XenonCodes\PHP2\Blog\UUID;
use XenonCodes\PHP2\Person\Name;

class CreateUserCommandTest extends TestCase
{
    // Проверяем, что команда создания пользователя бросает исключение,
    // если пользователь с таким именем уже существует
    public function testItThrowsAnExceptionWhenUserAlreadyExists(): void
    {
        // Создаём объект команды
        // У команды одна зависимость - UsersRepositoryInterface
        $command = new CreateUserCommand(
            $usersRepository = new class implements UsersRepositoryInterface  // здесь должна быть реализация UsersRepositoryInterface
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
                    return new User(UUID::random(), new Name("first", "last"), "user123", new DateTimeImmutable());
                }
            }
        );
        // Описываем тип ожидаемого исключения
        $this->expectException(CommandException::class);
        // и его сообщение
        $this->expectExceptionMessage('User already exists: Ivan');
        // Запускаем команду с аргументами
        $command->handle(new Arguments(['login' => 'Ivan']));
    }

    // Тест проверяет, что команда действительно требует имя пользователя
    public function testItRequiresFirstName(): void
    {
        // Передаём объект анонимного класса
        // в качестве реализации UsersRepositoryInterface
        $command = new CreateUserCommand($this->makeUsersRepository());
        // Ожидаем, что будет брошено исключение
        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage('No such argument: first_name');
        // Запускаем команду
        $command->handle(new Arguments(['login' => 'Ivan']));
    }

    // Тест проверяет, что команда действительно требует фамилию пользователя
    public function testItRequiresLastName(): void
    {
        // Передаём в конструктор команды объект, возвращаемый нашей функцией
        $command = new CreateUserCommand($this->makeUsersRepository());
        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage('No such argument: last_name');
        $command->handle(new Arguments([
            'login' => 'Ivan',
            // Нам нужно передать имя пользователя,
            // чтобы дойти до проверки наличия фамилии
            'first_name' => 'Ivan',
        ]));
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
            // Этого метода нет в контракте UsersRepositoryInterface,
            // но ничто не мешает его добавить.
            // С помощью этого метода мы можем узнать,
            // был ли вызван метод save
            public function wasCalled(): bool
            {
                return $this->called;
            }
        };
        // Передаём наш мок в команду
        $command = new CreateUserCommand($usersRepository);
        // Запускаем команду
        $command->handle(new Arguments([
            'login' => 'Ivan',
            'first_name' => 'Ivan',
            'last_name' => 'Nikitin',
        ]));
        // Проверяем утверждение относительно мока,
        // а не утверждение относительно команды
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
        };
    }
}
