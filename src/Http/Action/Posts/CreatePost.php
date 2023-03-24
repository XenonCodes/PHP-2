<?php

namespace XenonCodes\PHP2\Http\Action\Posts;

use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use XenonCodes\PHP2\Blog\Exceptions\AuthException;
use XenonCodes\PHP2\Blog\Exceptions\HttpException;
use XenonCodes\PHP2\Blog\Exceptions\UserNotFoundException;
use XenonCodes\PHP2\Blog\Post;
use XenonCodes\PHP2\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use XenonCodes\PHP2\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use XenonCodes\PHP2\Blog\UUID;
use XenonCodes\PHP2\Http\Action\ActionInterface;
use XenonCodes\PHP2\Http\Auth\AuthenticationInterface;
use XenonCodes\PHP2\Http\Auth\IdentificationInterface;
use XenonCodes\PHP2\Http\Auth\TokenAuthenticationInterface;
use XenonCodes\PHP2\Http\ErrorResponse;
use XenonCodes\PHP2\Http\Request;
use XenonCodes\PHP2\Http\Response;
use XenonCodes\PHP2\Http\SuccessfulResponse;

class CreatePost implements ActionInterface
{
    public function __construct(
        private PostsRepositoryInterface $postsRepository,
        // Аутентификация по токену
        private TokenAuthenticationInterface $authentication,
        // Внедряем контракт логгера
        private LoggerInterface $logger,

    ) {
    }
    public function handle(Request $request): Response
    {
        // Обрабатываем ошибки аутентификации
        // и возвращаем неудачный ответ
        // с сообщением об ошибке
        try {
            $author = $this->authentication->user($request);
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }


        $newPostUuid = UUID::random();
        try {
            $post = new Post(
                $newPostUuid,
                $author,
                $request->jsonBodyField('title'),
                $request->jsonBodyField('text'),
            );
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $this->postsRepository->save($post);

        // Логируем UUID новой статьи
        $this->logger->info("Post created: $newPostUuid");

        return new SuccessfulResponse([
            'uuid' => (string)$newPostUuid,
        ]);
    }
}
