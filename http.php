<?php

use XenonCodes\PHP2\Http\Action\Posts\DeletePost;
use XenonCodes\PHP2\Blog\Exceptions\AppException;
use XenonCodes\PHP2\Blog\Exceptions\HttpException;
use XenonCodes\PHP2\Http\Action\Comments\CreateComment;
use XenonCodes\PHP2\Http\Action\Comments\DeleteComment;
use XenonCodes\PHP2\Http\Action\Likes\CreateLike;
use XenonCodes\PHP2\Http\Action\Likes\DeleteLike;
use XenonCodes\PHP2\Http\Action\Likes\FindByPost;
use XenonCodes\PHP2\Http\Action\Posts\CreatePost;
use XenonCodes\PHP2\Http\Action\Users\FindByLogin;
use XenonCodes\PHP2\Http\Action\Users\CreateUser;
use XenonCodes\PHP2\Http\ErrorResponse;
use XenonCodes\PHP2\Http\Request;

$container = require __DIR__ . '/bootstrap.php';

$request = new Request($_GET, $_SERVER, file_get_contents('php://input'));

try {
    $path = $request->path();
} catch (HttpException) {
    (new ErrorResponse())->send();
    return;
}

try {
    $method = $request->method();
} catch (HttpException) {
    (new ErrorResponse)->send();
    return;
}

$routes = [
    'GET' => [
        '/users/show' => FindByLogin::class,
        // '/posts/show' => FindByUuid::class,
        // '/posts/show' => FindByPost::class,
    ],
    'POST' => [
        '/posts/create' => CreatePost::class,
        '/posts/likes/create' => CreateLike::class,
        '/posts/comments/create' => CreateComment::class,
        '/users/create' => CreateUser::class,
    ],
    'DELETE' => [
        '/posts/delete' => DeletePost::class,
        '/posts/comments/delete' => DeleteComment::class,
        '/posts/likes/delete' => DeleteLike::class,
    ],
];

if (!array_key_exists($method, $routes)) {
    (new ErrorResponse("Not found: $method $path"))->send();
    return;
}

if (!array_key_exists($path, $routes[$method])) {
    (new ErrorResponse("Not found: $method $path"))->send();
    return;
}

$actionClassName = $routes[$method][$path];

$action = $container->get($actionClassName);

$response = null;

try {
    $response = $action->handle($request);
} catch (AppException $e) {
    (new ErrorResponse($e->getMessage()))->send();
}

if ($response !== null) {
    $response->send();
}
