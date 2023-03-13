<?php

namespace XenonCodes\PHP2\Tests\Repositories;

use DateTimeImmutable;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use XenonCodes\PHP2\Blog\Exceptions\PostNotFoundException;
use XenonCodes\PHP2\Blog\Post;
use XenonCodes\PHP2\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use XenonCodes\PHP2\Blog\User;
use XenonCodes\PHP2\Blog\UUID;
use XenonCodes\PHP2\Person\Name;

class SqlitePostsRepositoryTest extends TestCase
{
    public function testItSavesPostToDatabase(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);

        $statementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':uuid' => '11111111-1111-1111-1111-111111111111',
                ':author_uuid' => '22222222-2222-2222-2222-222222222222',
                ':title' => 'Заголовок',
                ':text' => 'Текст...',
            ]);

        $connectionStub->method('prepare')->willReturn($statementMock);
        $repository = new SqlitePostsRepository($connectionStub);

        $user = new User(
            new UUID('22222222-2222-2222-2222-222222222222'),
            new Name('OneName', 'TwoName'),
            'login',
            new DateTimeImmutable(),
        );

        $repository->save(
            new Post(
                new UUID('11111111-1111-1111-1111-111111111111'),
                $user,
                'Заголовок',
                'Текст...'
            )
        );
    }

    public function testItGetPostByUuid(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);

        $statementMock->method('fetch')->willReturn([
            'uuid' => '11111111-1111-1111-1111-111111111111',
            'author_uuid' => '22222222-2222-2222-2222-222222222222',
            'title' => 'Заголовок',
            'text' => 'Текст...',
            'login' => 'logib',
            'first_name' => 'OneName',
            'last_name' => 'TwoName',
        ]);

        $connectionStub->method('prepare')->willReturn($statementMock);

        $postRepository = new SqlitePostsRepository($connectionStub);
        $post = $postRepository->get(new UUID('11111111-1111-1111-1111-111111111111'));

        $this->assertSame('11111111-1111-1111-1111-111111111111', (string)$post->getId());
    }

    public function testItThrowsAnExceptionWhenPostNotFound(): void
    {
        $connectionMock = $this->createStub(PDO::class);
        $statementStub = $this->createStub(PDOStatement::class);

        $statementStub->method('fetch')->willReturn(false);
        $connectionMock->method('prepare')->willReturn($statementStub);

        $repository = new SqlitePostsRepository($connectionMock);

        $this->expectExceptionMessage('Пост 11111111-1111-1111-1111-111111111111 не найден.');
        $this->expectException(PostNotFoundException::class);
        $repository->get(new UUID('11111111-1111-1111-1111-111111111111'));
    }
}
