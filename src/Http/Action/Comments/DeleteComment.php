<?php

namespace XenonCodes\PHP2\Http\Action\Comments;

use XenonCodes\PHP2\Blog\Exceptions\CommentNotFoundException;
use XenonCodes\PHP2\Blog\Repositories\CommentsRepository\CommentsRepositoryInterface;
use XenonCodes\PHP2\Blog\UUID;
use XenonCodes\PHP2\Http\Action\ActionInterface;
use XenonCodes\PHP2\Http\ErrorResponse;
use XenonCodes\PHP2\Http\Request;
use XenonCodes\PHP2\Http\Response;
use XenonCodes\PHP2\Http\SuccessfulResponse;

class DeleteComment implements ActionInterface
{
    public function __construct(
        private CommentsRepositoryInterface $commentsRepository
    )
    {
    }


    public function handle(Request $request): Response
    {
        try {
            $commentUuid = $request->query('uuid');
            $this->commentsRepository->get(new UUID($commentUuid));

        } catch (commentNotFoundException $error) {
            return new ErrorResponse($error->getMessage());
        }

        $this->commentsRepository->delete(new UUID($commentUuid));

        return new SuccessfulResponse([
            'uuid' => $commentUuid,
        ]);
    }
}