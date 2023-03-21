<?php

namespace XenonCodes\PHP2\Tests\Repositories;

use DateTimeImmutable;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use XenonCodes\PHP2\Blog\Comment;
use XenonCodes\PHP2\Blog\Exceptions\CommentNotFoundException;
use XenonCodes\PHP2\Blog\Post;
use XenonCodes\PHP2\Blog\Repositories\CommentsRepository\SqliteCommentsRepository;
use XenonCodes\PHP2\Blog\User;
use XenonCodes\PHP2\Blog\UUID;
use XenonCodes\PHP2\Person\Name;
use XenonCodes\PHP2\Tests\DummyLogger;

class SqliteCommentsRepositoryTest extends TestCase
{
    public function testItSavesCommentToDatabase(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);

        $statementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':uuid' => '33333333-3333-3333-3333-333333333333',
                ':post_uuid' => '11111111-1111-1111-1111-111111111111',
                ':author_uuid' => '22222222-2222-2222-2222-222222222222',
                ':text' => 'Текст...',
            ]);

        $connectionStub->method('prepare')->willReturn($statementMock);
        $repository = new SqliteCommentsRepository($connectionStub, new DummyLogger());

        $user = new User(
            new UUID('22222222-2222-2222-2222-222222222222'),
            new Name('OneName', 'TwoName'),
            'login',
            'password123',
            new DateTimeImmutable(),
        );

        $post = new Post(
            new UUID('11111111-1111-1111-1111-111111111111'),
            $user,
            'Заголовок',
            'Текст...',
        );

        $repository->save(
            new Comment(
                new UUID('33333333-3333-3333-3333-333333333333'),
                $user,
                $post,
                'Текст...'
            )
        );
    }

    public function testItGetCommentByUuid(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);

        $statementMock->method('fetch')->willReturn([
            'uuid' => '33333333-3333-3333-3333-333333333333',
            'post_uuid' => '22222222-2222-2222-2222-222222222222',
            'author_uuid' => '11111111-1111-1111-1111-111111111111',
            'title' => 'Заголовок',
            'text' => 'Текст...',
            'login' => 'login',
            'password' => 'password123',
            'first_name' => 'OneName',
            'last_name' => 'TwoName',
        ]);

        $connectionStub->method('prepare')->willReturn($statementMock);

        $commentRepository = new SqliteCommentsRepository($connectionStub, new DummyLogger());
        $comment = $commentRepository->get(new UUID('33333333-3333-3333-3333-333333333333'));

        $this->assertSame('33333333-3333-3333-3333-333333333333', (string)$comment->getId());
    }

    public function testItThrowsAnExceptionWhenCommentNotFound(): void
    {
        $connectionMock = $this->createStub(PDO::class);
        $statementStub = $this->createStub(PDOStatement::class);

        $statementStub->method('fetch')->willReturn(false);
        $connectionMock->method('prepare')->willReturn($statementStub);

        $repository = new SqliteCommentsRepository($connectionMock, new DummyLogger());

        $this->expectExceptionMessage('Комментарий 33333333-3333-3333-3333-333333333333 не найден.');
        $this->expectException(CommentNotFoundException::class);
        $repository->get(new UUID('33333333-3333-3333-3333-333333333333'));
    }
}
