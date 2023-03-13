<?php

require_once __DIR__ . '/vendor/autoload.php';

use XenonCodes\PHP2\Blog\Commands\CreateUserCommand;
use XenonCodes\PHP2\Blog\Commands\Arguments;
use XenonCodes\PHP2\Blog\Exceptions\AppException;
use XenonCodes\PHP2\Blog\Repositories\CommentsRepository\SqliteCommentsRepository;
use XenonCodes\PHP2\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use XenonCodes\PHP2\Blog\Repositories\UsersRepository\SqliteUsersRepository;

$faker = Faker\Factory::create('ru_RU');

$connection = new PDO('sqlite:' . __DIR__ . '/blog.sqlite');

$usersRepository = new SqliteUsersRepository($connection);
$postsRepository = new SqlitePostsRepository($connection);
$commentsRepository = new SqliteCommentsRepository($connection);

$container = require __DIR__ . '/bootstrap.php';

$command = $container->get(CreateUserCommand::class);

try {
    $command->handle(Arguments::fromArgv($argv));
} catch (AppException $e) {
    echo "{$e->getMessage()}\n";
}