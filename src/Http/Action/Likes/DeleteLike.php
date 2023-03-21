<?php

namespace XenonCodes\PHP2\Http\Action\Likes;

use XenonCodes\PHP2\Blog\Exceptions\AuthException;
use XenonCodes\PHP2\Blog\Exceptions\LikeNotFoundException;
use XenonCodes\PHP2\Blog\Repositories\LikesRepository\LikesRepositoryInterface;
use XenonCodes\PHP2\Blog\UUID;
use XenonCodes\PHP2\Http\Action\ActionInterface;
use XenonCodes\PHP2\Http\Auth\TokenAuthenticationInterface;
use XenonCodes\PHP2\Http\ErrorResponse;
use XenonCodes\PHP2\Http\Request;
use XenonCodes\PHP2\Http\Response;
use XenonCodes\PHP2\Http\SuccessfulResponse;

class DeleteLike implements ActionInterface
{
    public function __construct(
        private LikesRepositoryInterface $likesRepository,
        private TokenAuthenticationInterface $authentication
    ) {
    }


    public function handle(Request $request): Response
    {
        try {
            $this->authentication->user($request);
        } catch (AuthException $exception) {
            return  new  ErrorResponse($exception->getMessage());
        }

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
