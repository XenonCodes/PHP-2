<?php

namespace XenonCodes\PHP2\Http\Action\Likes;

use InvalidArgumentException;
use XenonCodes\PHP2\Blog\Exceptions\HttpException;
use XenonCodes\PHP2\Blog\Exceptions\PostNotFoundException;
use XenonCodes\PHP2\Blog\Exceptions\UserNotFoundException;
use XenonCodes\PHP2\Blog\Like;
use XenonCodes\PHP2\Blog\Repositories\LikesRepository\LikesRepositoryInterface;
use XenonCodes\PHP2\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use XenonCodes\PHP2\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use XenonCodes\PHP2\Blog\UUID;
use XenonCodes\PHP2\Http\Action\ActionInterface;
use XenonCodes\PHP2\Http\ErrorResponse;
use XenonCodes\PHP2\Http\Request;
use XenonCodes\PHP2\Http\Response;
use XenonCodes\PHP2\Http\SuccessfulResponse;

class CreateLike implements ActionInterface
{
    public function __construct(
        private LikesRepositoryInterface $likesRepository,
        private PostsRepositoryInterface $postsRepository,
        private UsersRepositoryInterface $usersRepository,
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            $authorUuid = new UUID($request->jsonBodyField('author_uuid'));
        } catch (HttpException | InvalidArgumentException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $this->usersRepository->get($authorUuid);
        } catch (UserNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $postUuid = new UUID($request->jsonBodyField('post_uuid'));
        } catch (HttpException | InvalidArgumentException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $this->postsRepository->get($postUuid);
        } catch (PostNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $newLikeUuid = UUID::random();

            $like = new Like(
                $newLikeUuid,
                $post = $this->postsRepository->get($postUuid),
                $author = $this->usersRepository->get($authorUuid),
            );

        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());

        }

        $this->likesRepository->save($like);

        return new SuccessfulResponse([
            'uuid' => (string)$newLikeUuid,
        ]);
    }
}