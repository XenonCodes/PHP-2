<?php

require_once __DIR__ . '/vendor/autoload.php';

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Application;
use XenonCodes\PHP2\Blog\Commands\CreateUserCommand;
use XenonCodes\PHP2\Blog\Commands\Arguments;
use XenonCodes\PHP2\Blog\Commands\FakeData\PopulateDB;
use XenonCodes\PHP2\Blog\Commands\Posts\DeletePost;
use XenonCodes\PHP2\Blog\Commands\Users\CreateUser;
use XenonCodes\PHP2\Blog\Commands\Users\UpdateUser;
use XenonCodes\PHP2\Blog\Exceptions\AppException;

$container = require __DIR__ . '/bootstrap.php';

//php cli.php login=admin first_name=Koly last_name=Petrov

// Получаем объект логгера из контейнера
$logger = $container->get(LoggerInterface::class);

// Создаём объект приложения
$application = new Application();
// Перечисляем классы команд
$commandsClasses = [
    CreateUser::class,
    DeletePost::class,
    UpdateUser::class,
    PopulateDB::class,
];
foreach ($commandsClasses as $commandClass) {
    // Посредством контейнера
    // создаём объект команды
    $command = $container->get($commandClass);
    // Добавляем команду к приложению
    $application->add($command);
}


try {
    // Запускаем приложение
    $application->run();
} catch (AppException $e) {
    echo "{$e->getMessage()}\n";
    // Логируем информацию об исключении.
    // Объект исключения передаётся логгеру
    // с ключом "exception".
    // Уровень логирования – ERROR
    $logger->error($e->getMessage(), ['exception' => $e]);
}
