<?php

namespace XenonCodes\PHP2\Tests\Action;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use XenonCodes\PHP2\Blog\Exceptions\UserNotFoundException;
use XenonCodes\PHP2\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use XenonCodes\PHP2\Blog\User;
use XenonCodes\PHP2\Blog\UUID;
use XenonCodes\PHP2\Http\Action\Users\FindByLogin;
use XenonCodes\PHP2\Http\ErrorResponse;
use XenonCodes\PHP2\Http\Request;
use XenonCodes\PHP2\Http\SuccessfulResponse;
use XenonCodes\PHP2\Person\Name;

class FindByLoginActionTest extends TestCase
{
    // Запускаем тест в отдельном процессе
    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    // Тест, проверяющий, что будет возвращён неудачный ответ,
    // если в запросе нет параметра login
    public function testItReturnsErrorResponseIfNoLoginProvided(): void
    {
        // Создаём объект запроса
        // Вместо суперглобальных переменных
        // передаём простые массивы
        $request = new Request([], [],'');
        // Создаём стаб репозитория пользователей
        $usersRepository = $this->usersRepository([]);
        //Создаём объект действия
        $action = new FindByLogin($usersRepository);
        // Запускаем действие
        $response = $action->handle($request);
        // Проверяем, что ответ - неудачный
        $this->assertInstanceOf(ErrorResponse::class, $response);
        // Описываем ожидание того, что будет отправлено в поток вывода
        $this->expectOutputString('{"success":false,"reason":"Такого параметра запроса в запросе нет: login"}');
        // Отправляем ответ в поток вывода
        $response->send();
    }
    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    // Тест, проверяющий, что будет возвращён неудачный ответ,
    // если пользователь не найден
    public function testItReturnsErrorResponseIfUserNotFound(): void
    {
        // Теперь запрос будет иметь параметр login
        $request = new Request(['login' => 'ivan'], [],'');
        // Репозиторий пользователей по-прежнему пуст
        $usersRepository = $this->usersRepository([]);
        $action = new FindByLogin($usersRepository);
        $response = $action->handle($request);
        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString('{"success":false,"reason":"Not found"}');
        $response->send();
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    // Тест, проверяющий, что будет возвращён удачный ответ,
    // если пользователь найден
    public function testItReturnsSuccessfulResponse(): void
    {
        $request = new Request(['login' => 'ivan'], [],'');
        // На этот раз в репозитории есть нужный нам пользователь
        $usersRepository = $this->usersRepository([
            new User(
                UUID::random(),
                new Name('Ivan', 'Nikitin'),
                'ivan',
                new DateTimeImmutable()
            ),
        ]);
        $action = new FindByLogin($usersRepository);
        $response = $action->handle($request);
        // Проверяем, что ответ - удачный
        $this->assertInstanceOf(SuccessfulResponse::class, $response);
        $this->expectOutputString('{"success":true,"data":{"login":"ivan","name":"Ivan Nikitin"}}');
        $response->send();
    }
    // Функция, создающая стаб репозитория пользователей,
    // принимает массив "существующих" пользователей
    private function usersRepository(array $users): UsersRepositoryInterface
    {
        // В конструктор анонимного класса передаём массив пользователей
        return new class($users) implements UsersRepositoryInterface
        {
            public function __construct(
                private array $users
            ) {
            }
            public function save(User $user): void
            {
            }
            public function get(UUID $uuid): User
            {
                throw new UserNotFoundException("Not found");
            }
            public function getByLogin(string $login): User
            {
                foreach ($this->users as $user) {
                    if ($user instanceof User && $login === $user->getLogin()) {
                        return $user;
                    }
                }
                throw new UserNotFoundException("Not found");
            }
        };
    }
}
