<?php

namespace XenonCodes\PHP2\Http\Action\Users;

use DateTimeImmutable;
use XenonCodes\PHP2\Blog\Exceptions\CheckingDuplicateLoginException;
use XenonCodes\PHP2\Blog\Exceptions\HttpException;
use XenonCodes\PHP2\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use XenonCodes\PHP2\Blog\User;
use XenonCodes\PHP2\Blog\UUID;
use XenonCodes\PHP2\Http\Action\ActionInterface;
use XenonCodes\PHP2\Http\ErrorResponse;
use XenonCodes\PHP2\Http\Request;
use XenonCodes\PHP2\Http\Response;
use XenonCodes\PHP2\Http\SuccessfulResponse;
use XenonCodes\PHP2\Person\Name;

class CreateUser implements ActionInterface
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository,
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            $user = User::createFrom(
                $request->jsonBodyField('login'),
                $request->jsonBodyField('password'),
                new Name(
                    $request->jsonBodyField('first_name'),
                    $request->jsonBodyField('last_name')
                )
            );

        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $this->usersRepository->checkUser($request->jsonBodyField('login'));
        } catch (CheckingDuplicateLoginException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $this->usersRepository->save($user);

        return new SuccessfulResponse([
            'uuid' => (string)$user->getId(),
        ]);
    }
}