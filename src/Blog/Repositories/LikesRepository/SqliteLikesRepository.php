<?php

namespace XenonCodes\PHP2\Blog\Repositories\LikesRepository;

use PDO;
use PDOStatement;
use XenonCodes\PHP2\Blog\Exceptions\AlreadylikedThisException;
use XenonCodes\PHP2\Blog\Exceptions\LikeNotFoundException;
use XenonCodes\PHP2\Blog\Like;
use XenonCodes\PHP2\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use XenonCodes\PHP2\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use XenonCodes\PHP2\Blog\UUID;

class SqliteLikesRepository implements LikesRepositoryInterface
{
    public function __construct(
        private PDO $connection
    ) {
    }

    public function save(Like $like): void
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM likes WHERE post_uuid = :post_uuid AND author_uuid = :author_uuid'
        );
    
        $statement->execute([
            ':post_uuid' => $like->getPost()->getId(),
            ':author_uuid' => $like->getAuthor()->getId(),
        ]);
    
        $result = $statement->fetch(PDO::FETCH_ASSOC);
    
        if ($result) {
            throw new AlreadylikedThisException('Пользователю уже понравился этот пост.');
        }

        $statement = $this->connection->prepare(
            'INSERT INTO likes (uuid, post_uuid, author_uuid)
            VALUES (:uuid, :post_uuid, :author_uuid)'
        );

        $statement->execute([
            ':uuid' => $like->getId(),
            ':post_uuid' => $like->getPost()->getId(),
            ':author_uuid' => $like->getAuthor()->getId(),
        ]);
    }

    public function get(UUID $uuid): Like
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM likes WHERE uuid = :uuid'
        );

        $statement->execute([
            ':uuid' => (string)$uuid,
        ]);

        return $this->getLike($statement, $uuid);
    }

    private function getLike(PDOStatement $statement, string $like): Like
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if (!$result) {
            throw new LikeNotFoundException(
                "Like $like не найден."
            );
        }

        $usersRepository = new SqliteUsersRepository($this->connection);
        $postsRepository = new SqlitePostsRepository($this->connection);

        $user = $usersRepository->get(new UUID($result['author_uuid']));
        $post = $postsRepository->get(new UUID($result['post_uuid']));

        return new Like(
            new UUID($result['uuid']),
            $post,
            $user,
        );
    }

    public function delete(UUID $uuid): void
    {
        $statement = $this->connection->prepare(
            'DELETE FROM likes WHERE likes.uuid=:uuid;'
        );

        $statement->execute([
            ':uuid' => $uuid,
        ]);
    }

    public function getByPostUuid(string $postUuid): array
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM likes WHERE post_uuid = :postUuid'
        );

        $statement->execute([
            ':postUuid' => $postUuid
        ]);

        $likesData = $statement->fetchAll(PDO::FETCH_ASSOC);

        $usersRepository = new SqliteUsersRepository($this->connection);
        $postsRepository = new SqlitePostsRepository($this->connection);

        $likes = [];

        foreach ($likesData as $likeData) {
            $user = $usersRepository->get(new UUID($likeData['author_uuid']));
            $post = $postsRepository->get(new UUID($likeData['post_uuid']));

            $likes[] = new Like(
                new UUID($likeData['uuid']),
                $post,
                $user,
            );
        }

        return $likes;
    }
}
