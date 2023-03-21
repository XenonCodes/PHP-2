<?php

namespace XenonCodes\PHP2\Http\Action\Posts;

use XenonCodes\PHP2\Blog\Exceptions\AuthException;
use XenonCodes\PHP2\Blog\Exceptions\PostNotFoundException;
use XenonCodes\PHP2\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use XenonCodes\PHP2\Blog\UUID;
use XenonCodes\PHP2\Http\Action\ActionInterface;
use XenonCodes\PHP2\Http\Auth\TokenAuthenticationInterface;
use XenonCodes\PHP2\Http\ErrorResponse;
use XenonCodes\PHP2\Http\Request;
use XenonCodes\PHP2\Http\Response;
use XenonCodes\PHP2\Http\SuccessfulResponse;

class DeletePost implements ActionInterface
{
    public function __construct(
        private PostsRepositoryInterface $postsRepository,
        private TokenAuthenticationInterface $authentication
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            $authenticatedUser = $this->authentication->user($request);
        } catch (AuthException $exception) {
            return  new  ErrorResponse($exception->getMessage());
        }

        try {
            $postUuid = $request->query('uuid');
            $post = $this->postsRepository->get(new UUID($postUuid));
        } catch (PostNotFoundException $error) {
            return new ErrorResponse($error->getMessage());
        }

        // Проверьте, является ли аутентифицированный пользователь автором сообщения
        if ($authenticatedUser->getId() !== $post->getAuthor()->getId()) {
            return new ErrorResponse('Вы не уполномочены удалять этот пост!');
        }

        $this->postsRepository->delete(new UUID($postUuid));

        return new SuccessfulResponse([
            'uuid' => $postUuid,
        ]);
    }
}
