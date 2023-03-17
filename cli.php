<?php

require_once __DIR__ . '/vendor/autoload.php';

use Psr\Log\LoggerInterface;
use XenonCodes\PHP2\Blog\Commands\CreateUserCommand;
use XenonCodes\PHP2\Blog\Commands\Arguments;
use XenonCodes\PHP2\Blog\Exceptions\AppException;

$container = require __DIR__ . '/bootstrap.php';

//php cli.php login=admin first_name=Koly last_name=Petrov

// Получаем объект логгера из контейнера
$logger = $container->get(LoggerInterface::class);

try {
    $command = $container->get(CreateUserCommand::class);
    $command->handle(Arguments::fromArgv($argv));
} catch (AppException $e) {
    echo "{$e->getMessage()}\n";
    // Логируем информацию об исключении.
    // Объект исключения передаётся логгеру
    // с ключом "exception".
    // Уровень логирования – ERROR
    $logger->error($e->getMessage(), ['exception' => $e]);
}
