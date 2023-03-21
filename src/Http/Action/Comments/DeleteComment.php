<?php

namespace XenonCodes\PHP2\Http\Action\Comments;

use XenonCodes\PHP2\Blog\Exceptions\AuthException;
use XenonCodes\PHP2\Blog\Exceptions\CommentNotFoundException;
use XenonCodes\PHP2\Blog\Repositories\CommentsRepository\CommentsRepositoryInterface;
use XenonCodes\PHP2\Blog\UUID;
use XenonCodes\PHP2\Http\Action\ActionInterface;
use XenonCodes\PHP2\Http\Auth\TokenAuthenticationInterface;
use XenonCodes\PHP2\Http\ErrorResponse;
use XenonCodes\PHP2\Http\Request;
use XenonCodes\PHP2\Http\Response;
use XenonCodes\PHP2\Http\SuccessfulResponse;

class DeleteComment implements ActionInterface
{
    public function __construct(
        private CommentsRepositoryInterface $commentsRepository,
        private TokenAuthenticationInterface $authentication
    )
    {
    }


    public function handle(Request $request): Response
    {
        try {
            $authenticatedUser = $this->authentication->user($request);
        } catch (AuthException $exception) {
            return  new  ErrorResponse($exception->getMessage());
        }

        try {
            $commentUuid = $request->query('uuid');
            $comment = $this->commentsRepository->get(new UUID($commentUuid));

        } catch (commentNotFoundException $error) {
            return new ErrorResponse($error->getMessage());
        }

        // Проверьте, является ли аутентифицированный пользователь автором комментария
        if ($authenticatedUser->getId() !== $comment->getAuthor()->getId()) {
            return new ErrorResponse('Вы не уполномочены удалять этот комментарий!');
        }

        $this->commentsRepository->delete(new UUID($commentUuid));

        return new SuccessfulResponse([
            'uuid' => $commentUuid,
        ]);
    }
}