<?php

namespace XenonCodes\PHP2\Blog\Repositories\CommentsRepository;

use PDO;
use PDOStatement;
use XenonCodes\PHP2\Blog\Comment;
use XenonCodes\PHP2\Blog\Exceptions\CommentNotFoundException;
use XenonCodes\PHP2\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use XenonCodes\PHP2\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use XenonCodes\PHP2\Blog\UUID;

class SqliteCommentsRepository implements CommentsRepositoryInterface
{
    public function __construct(
        private PDO $connection
    ) {
    }

    public function save(Comment $comment): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO comments (uuid, post_uuid, author_uuid, text)
            VALUES (:uuid, :post_uuid, :author_uuid, :text)'
        );

        $statement->execute([
            ':uuid' => $comment->getId(),
            ':post_uuid' => $comment->getPost()->getId(),
            ':author_uuid' => $comment->getAuthor()->getId(),
            ':text' => $comment->getText(),
        ]);
    }

    public function get(UUID $uuid): Comment
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM comments WHERE uuid = :uuid'
        );
        $statement->execute([
            ':uuid' => (string)$uuid,
        ]);

        return $this->getComment($statement, $uuid);
    }

    private function getComment(PDOStatement $statement, string $comment): Comment
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if (!$result) {
            throw new CommentNotFoundException(
                "Комментарий $comment не найден."
            );
        }

        $usersRepository = new SqliteUsersRepository($this->connection);
        $postsRepository = new SqlitePostsRepository($this->connection);

        $user = $usersRepository->get(new UUID($result['author_uuid']));
        $post = $postsRepository->get(new UUID($result['post_uuid']));

        return new Comment(
            new UUID($result['uuid']),
            $user,
            $post,
            $result['text'],
        );
    }

    public function delete(UUID $uuid): void
    {
        $statement = $this->connection->prepare(
            'DELETE FROM comments WHERE comments.uuid=:uuid;'
        );

        $statement->execute([
            ':uuid' => $uuid,
        ]);
    }
}
