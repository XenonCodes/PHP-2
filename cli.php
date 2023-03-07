<?php

require_once __DIR__ . '/vendor/autoload.php';

use XenonCodes\PHP2\Blog\Commands\CreateUserCommand;
use XenonCodes\PHP2\Blog\Commands\Arguments;
use XenonCodes\PHP2\Blog\Comment;
use XenonCodes\PHP2\Blog\Post;
use XenonCodes\PHP2\Blog\Repositories\CommentsRepository\SqliteCommentsRepository;
use XenonCodes\PHP2\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use XenonCodes\PHP2\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use XenonCodes\PHP2\Blog\User;
use XenonCodes\PHP2\Blog\UUID;
use XenonCodes\PHP2\Person\Name;

$faker = Faker\Factory::create('ru_RU');

$connection = new PDO('sqlite:' . __DIR__ . '/blog.sqlite');

$usersRepository = new SqliteUsersRepository($connection);
$postsRepository = new SqlitePostsRepository($connection);
$commentsRepository = new SqliteCommentsRepository($connection);

//------------------------сохранить в БД случайных User/Post/Comment--------------------------------
// $name = new Name($faker->firstName(), $faker->lastName());
// $user = new User(UUID::random(), $name, $faker->userName(), new DateTimeImmutable());
// $usersRepository->save($user);

// $post = new Post(
//     UUID::random(),
//     $usersRepository->getByLogin('vitalii67'), //User c ником vitalii67 уже есть в БД
//     $faker->realText(rand(10, 25)),
//     $faker->realText(rand(100, 200)),
// );
// $postsRepository->save($post);

// $comment = new Comment(
//     UUID::random(),
//     $usersRepository->getByLogin('pakomova.iskra'), //User c ником pakomova.iskra уже есть в БД
//     $postsRepository->get(new UUID('ba7467a4-f315-4c57-b191-2728fd4eb402')), //Post cданным UUID уже есть
//     $faker->realText(rand(100, 200)),
// );
// $commentsRepository->save($comment);

//-----------------------------получить из БД User/Post/Comment-------------------------------------
try {
    echo $usersRepository->getByLogin('admin') . PHP_EOL;
    echo '---------------------' . PHP_EOL;
    echo $postsRepository->get(new UUID('ba7467a4-f315-4c57-b191-2728fd4eb402')) . PHP_EOL;
    echo '---------------------' . PHP_EOL;
    echo $commentsRepository->get(new UUID('52772b9c-1a0a-433a-924e-88f462aae528')) . PHP_EOL;
} catch (Exception $e) {
    print $e->getMessage();
}

//----------------------------------тут работаем с command---------------------------------------
//Команда в терминале php cli.php login=ВАШ_ЛГИН first_name=ВАШЕ_ИМЯ last_name=ВАШЕ_ФАМИЛИЯ  
// $command = new CreateUserCommand($usersRepository);

// try {
//     $command->handle(Arguments::fromArgv($argv));
// } catch (Exception $e) {
//     print $e->getMessage();
// }