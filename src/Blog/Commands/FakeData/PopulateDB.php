<?php

namespace XenonCodes\PHP2\Blog\Commands\FakeData;

use Faker\Generator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use XenonCodes\PHP2\Blog\Comment;
use XenonCodes\PHP2\Blog\Post;
use XenonCodes\PHP2\Blog\Repositories\CommentsRepository\CommentsRepositoryInterface;
use XenonCodes\PHP2\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use XenonCodes\PHP2\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use XenonCodes\PHP2\Blog\User;
use XenonCodes\PHP2\Blog\UUID;
use XenonCodes\PHP2\Person\Name;

class PopulateDB extends Command
{
    // Внедряем генератор тестовых данных и
    // репозитории пользователей и статей
    public function __construct(
        private Generator $faker,
        private UsersRepositoryInterface $usersRepository,
        private PostsRepositoryInterface $postsRepository,
        private CommentsRepositoryInterface $commentsRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('fake-data:populate-db')
            ->setDescription('Populates DB with fake data')
            ->addOption(
                // Имя опции
                'users-number',
                // Сокращённое имя
                'u',
                // Опция имеет значения
                InputOption::VALUE_OPTIONAL,
                // Описание
                'Number of users being created',
            )
            ->addOption(
                'posts-number',
                'p',
                InputOption::VALUE_OPTIONAL,
                'Number of posts being created',
            )
            ->addOption(
                'comments-number',
                'c',
                InputOption::VALUE_OPTIONAL,
                'Number of comments being created',
            );
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output,
    ): int {
        // Получаем значения опций
        $userNumber = $input->getOption('users-number');
        $postNumber = $input->getOption('posts-number');
        $commentNumber = $input->getOption('comments-number');

        if (empty($userNumber)) {
            $userNumber = 5;
        }

        // Создаём Num пользователей
        $users = [];
        for ($i = 0; $i < $userNumber; $i++) {
            $user = $this->createFakeUser();
            $users[] = $user;
            $output->writeln('User created: ' . $user->getName());
        }

        if (empty($postNumber)) {
            $postNumber = 2;
        }
        if (empty($commentNumber)) {
            $commentNumber = 3;
        }

        // От имени каждого пользователя
        // создаём по Num статей и Num комментов
        foreach ($users as $user) {
            for ($i = 0; $i < $postNumber; $i++) {
                $post = $this->createFakePost($user);
                $output->writeln('Post created: ' . $post->getTitle());
                for ($j = 0; $j<$commentNumber;$j++) {
                    $comment = $this->createFakeComment($user, $post);
                    $output->writeln('Comment created: ' . $comment->getId());
                }
            }
        }

        return Command::SUCCESS;
    }

    private function createFakeUser(): User
    {
        $user = User::createFrom(
            // Генерируем имя пользователя
            $this->faker->userName,
            // Генерируем пароль
            $this->faker->password,
            new Name(
                // Генерируем имя
                $this->faker->firstName,
                // Генерируем фамилию
                $this->faker->lastName
            )
        );
        // Сохраняем пользователя в репозиторий
        $this->usersRepository->save($user);
        return $user;
    }

    private function createFakePost(User $author): Post
    {
        $post = new Post(
            UUID::random(),
            $author,
            // Генерируем предложение не длиннее шести слов
            $this->faker->sentence(6, true),
            // Генерируем текст
            $this->faker->realText
        );

        // Сохраняем статью в репозиторий
        $this->postsRepository->save($post);
        return $post;
    }

    private function createFakeComment(User $author, Post $post): Comment
    {
        $comment = new Comment(
            UUID::random(),
            $author,
            $post,
            // Генерируем текст
            $this->faker->realText
        );

        // Сохраняем статью в репозиторий
        $this->commentsRepository->save($comment);
        return $comment;
    }
}
