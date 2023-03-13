<?php

use XenonCodes\PHP2\Http\Action\Posts\DeletePost;
use XenonCodes\PHP2\Blog\Exceptions\AppException;
use XenonCodes\PHP2\Blog\Exceptions\HttpException;
use XenonCodes\PHP2\Blog\Repositories\CommentsRepository\SqliteCommentsRepository;
use XenonCodes\PHP2\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use XenonCodes\PHP2\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use XenonCodes\PHP2\Http\Action\Comments\CreateComment;
use XenonCodes\PHP2\Http\Action\Posts\CreatePost;
use XenonCodes\PHP2\Http\Action\Users\FindByLogin;
use XenonCodes\PHP2\Http\Action\Users\CreateUser;
use XenonCodes\PHP2\Http\ErrorResponse;
use XenonCodes\PHP2\Http\Request;
use XenonCodes\PHP2\Http\SuccessfulResponse;

require_once __DIR__ . '/vendor/autoload.php';

$request = new Request($_GET, $_SERVER, file_get_contents('php://input'));

// $parameter = $request->query('some_parameter');
// $header = $request->header('Some-Header');
// $path = $request->path();

// // Создаём объект ответа
// $response = new SuccessfulResponse([
//     'message' => 'Hello from PHP',
// ]);
// // Отправляем ответ
// $response->send();

try {
    // Пытаемся получить путь из запроса
    $path = $request->path();
} catch (HttpException) {
    // Отправляем неудачный ответ,
    // если по какой-то причине
    // не можем получить путь
    (new ErrorResponse())->send();
    // Выходим из программы
    return;
}

try {
    // Пытаемся получить HTTP-метод запроса
    $method = $request->method();
} catch (HttpException) {
    // Возвращаем неудачный ответ,
    // если по какой-то причине
    // не можем получить метод
    (new ErrorResponse)->send();
    return;
}


$routes = [
    'GET' => [
        '/users/show' => new FindByLogin(
            new SqliteUsersRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            )
        ),
        // '/posts/show' => new FindByUuid(
        //     new SqlitePostsRepository(
        //         new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
        //     )
        // ),
    ],
    'POST' => [
        // Добавили новый маршрут
        '/posts/create' => new CreatePost(
            new SqlitePostsRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            ),
            new SqliteUsersRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            )
        ),
        '/posts/comment' => new CreateComment(
            new SqliteCommentsRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            ),
            new SqlitePostsRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            ),
            new SqliteUsersRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            )
        ),
        '/users/create' => new CreateUser(
            new SqliteUsersRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            )
        ),
    ],
    'DELETE' => [
        '/posts' => new DeletePost(
            new SqlitePostsRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            ),
        ),
    ],

];

// Если у нас нет маршрутов для метода запроса -
// возвращаем неуспешный ответ
if (!array_key_exists($method, $routes)) {
    (new ErrorResponse('Not found'))->send();
    return;
}
// Ищем маршрут среди маршрутов для этого метода
if (!array_key_exists($path, $routes[$method])) {
    (new ErrorResponse('Not found'))->send();
    return;
}
// Выбираем действие по методу и пути
$action = $routes[$method][$path];
try {
    $response = $action->handle($request);
} catch (AppException $e) {
    (new ErrorResponse($e->getMessage()))->send();
}
$response->send();