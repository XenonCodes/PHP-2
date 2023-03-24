<?php

namespace XenonCodes\PHP2\Blog\Repositories\PostsRepository;

use PDO;
use PDOStatement;
use Psr\Log\LoggerInterface;
use XenonCodes\PHP2\Blog\Exceptions\PostNotFoundException;
use XenonCodes\PHP2\Blog\Post;
use XenonCodes\PHP2\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use XenonCodes\PHP2\Blog\UUID;

class SqlitePostsRepository implements PostsRepositoryInterface
{
    public function __construct(
        private PDO $connection, private LoggerInterface $logger
    ) {
    }

    public function save(Post $post): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO posts (uuid, author_uuid, title, text)
            VALUES (:uuid, :author_uuid, :title, :text)'
        );

        $statement->execute([
            ':uuid' => $post->getId(),
            ':author_uuid' => $post->getAuthor()->getId(),
            ':title' => $post->getTitle(),
            ':text' => $post->getText(),
        ]);

        $this->logger->info("Post created: {$post->getId()}");
    }

    public function get(UUID $uuid): Post
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM posts WHERE uuid = :uuid'
        );
        $statement->execute([
            ':uuid' => (string)$uuid,
        ]);

        return $this->getPost($statement, $uuid);
    }

    private function getPost(PDOStatement $statement, string $post): Post
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if (!$result) {
            $this->logger->warning("Пост $post не найден.");
            throw new PostNotFoundException(
                "Пост $post не найден."
            );
        }

        $usersRepository = new SqliteUsersRepository($this->connection, $this->logger);
        $user = $usersRepository->get(new UUID($result['author_uuid']));

        return new Post(
            new UUID($result['uuid']),
            $user,
            $result['title'],
            $result['text'],
        );
    }

    public function delete(UUID $uuid): void
    {
        $statement = $this->connection->prepare(
            "DELETE FROM posts
            WHERE uuid = :uuid;
            "
        );

        $statement->execute([
            ':uuid' => (string)$uuid,
        ]);

        $statement = $this->connection->prepare(
            "DELETE FROM comments
            WHERE post_uuid = :uuid;
            "
        );

        $statement->execute([
            ':uuid' => (string)$uuid,
        ]);
    }
}
