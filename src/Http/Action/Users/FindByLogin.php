<?php

namespace XenonCodes\PHP2\Http\Action\Users;

use XenonCodes\PHP2\Blog\Exceptions\HttpException;
use XenonCodes\PHP2\Blog\Exceptions\UserNotFoundException;
use XenonCodes\PHP2\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use XenonCodes\PHP2\Http\Action\ActionInterface;
use XenonCodes\PHP2\Http\ErrorResponse;
use XenonCodes\PHP2\Http\Request;
use XenonCodes\PHP2\Http\Response;
use XenonCodes\PHP2\Http\SuccessfulResponse;

class FindByLogin implements ActionInterface
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            $login = $request->query('login');
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }
        
        try {
            $user = $this->usersRepository->getByLogin($login);
        } catch (UserNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }
        
        return new SuccessfulResponse([
            'login' => $user->getLogin(),
            'name' => $user->getName()->getFirstName() . ' ' . $user->getName()->getLastName(),
        ]);
    }
}
