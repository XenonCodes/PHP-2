<?php

use XenonCodes\PHP2\Blog\Container\DIContainer;
use XenonCodes\PHP2\Blog\Repositories\CommentsRepository\CommentsRepositoryInterface;
use XenonCodes\PHP2\Blog\Repositories\CommentsRepository\SqliteCommentsRepository;
use XenonCodes\PHP2\Blog\Repositories\LikesRepository\LikesRepositoryInterface;
use XenonCodes\PHP2\Blog\Repositories\LikesRepository\SqliteLikesRepository;
use XenonCodes\PHP2\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use XenonCodes\PHP2\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use XenonCodes\PHP2\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use XenonCodes\PHP2\Blog\Repositories\UsersRepository\UsersRepositoryInterface;

require_once __DIR__ . '/vendor/autoload.php';

$container = new DIContainer();

$container->bind(
    PDO::class,
    new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
);

$container->bind(
    PostsRepositoryInterface::class,
    SqlitePostsRepository::class
);

$container->bind(
    UsersRepositoryInterface::class,
    SqliteUsersRepository::class
);

$container->bind(
    CommentsRepositoryInterface::class,
    SqliteCommentsRepository::class
);

$container->bind(
    LikesRepositoryInterface::class,
    SqliteLikesRepository::class
);

return $container;
