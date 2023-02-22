<?php

require_once __DIR__ . '/vendor/autoload.php';

use XenonCodes\PHP2\Blog\{Comment, Post, User};
use XenonCodes\PHP2\Person\{Name, Person};

/*
-Реализуйте автозагрузчик классов согласно следующим правилам:
    1. Разделитель пространства имён преобразуется в разделитель папок: / для Linux и
    MacOS или \ для Windows.
    2. Знак _ в имени класса преобразуется в разделитель папок.
    3. Файл с кодом класса имеет расширение .php.
-Примеры:
    1. \Doctrine\Common\ClassLoader ⇒ /some/path/Doctrine/Common/ClassLoader.php.
    2. \my\package\Class_Name ⇒ /some/path/namespace/package/Class/Name.php.
    3. \my\package_name\Class_Name ⇒ /some/path/my/package_name/Class/Name.php
*/

// use src\Blog\{Post, User};
// use src\Person\{Name, Person};

// spl_autoload_register(function ($class) {
//     // var_dump($class);
//     $file = str_replace('\\', DIRECTORY_SEPARATOR, $class);
//     $file = str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
//     var_dump($file);
//     if (file_exists($file)) {
//         require $file;
//     }
// });

// $person = new Person($name, new DateTimeImmutable());
// echo $person.PHP_EOL;
// echo '-----------------------------------'.PHP_EOL;

$faker = Faker\Factory::create('ru_RU');

$name = new Name($faker->firstName(), $faker->lastName());
$user = new User($faker->numberBetween(0, 100), $name, $faker->word(), new DateTimeImmutable() );

switch ($argv[1]) {
    case 'user':
        echo $user . "\n";
        break;

    case 'post':
        $post = new Post(
            $faker->numberBetween(0, 100),
            $user,
            $faker->realText(rand(10, 25)),
            $faker->realText(rand(100, 200))
        );
        echo $post . "\n";
        break;

    case 'comment':
        $name2 = new Name($faker->firstName(), $faker->lastName());
        $user2 = new User($faker->numberBetween(0, 100), $name2, $faker->word(), new DateTimeImmutable() );
        $post = new Post(
            $faker->numberBetween(0, 100),
            $user,
            $faker->realText(rand(10, 25)),
            $faker->realText(rand(100, 200))
        );
        $comment = new Comment(
            $faker->numberBetween(0, 100),
            $user2,
            $post,
            $faker->realText(rand(100, 200))
        );
        echo $comment . "\n";
        break;

    default:
        echo "Unknown command: " . $argv[1] . "\n";
        die();
}