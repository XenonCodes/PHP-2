<?php

namespace XenonCodes\PHP2\Http\Action\Likes;

use XenonCodes\PHP2\Blog\Exceptions\LikeNotFoundException;
use XenonCodes\PHP2\Blog\Repositories\LikesRepository\LikesRepositoryInterface;
use XenonCodes\PHP2\Blog\UUID;
use XenonCodes\PHP2\Http\Action\ActionInterface;
use XenonCodes\PHP2\Http\ErrorResponse;
use XenonCodes\PHP2\Http\Request;
use XenonCodes\PHP2\Http\Response;
use XenonCodes\PHP2\Http\SuccessfulResponse;

class DeleteLike implements ActionInterface
{
    public function __construct(
        private LikesRepositoryInterface $likesRepository
    ) {
    }


    public function handle(Request $request): Response
    {
        try {
            $likeUuid = $request->query('uuid');
            $this->likesRepository->get(new UUID($likeUuid));
        } catch (LikeNotFoundException $error) {
            return new ErrorResponse($error->getMessage());
        }

        $this->likesRepository->delete(new UUID($likeUuid));

        return new SuccessfulResponse([
            'uuid' => $likeUuid,
        ]);
    }
}
