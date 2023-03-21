<?php

namespace XenonCodes\PHP2\Http\Action\Auth;

use XenonCodes\PHP2\Blog\Exceptions\AuthException;
use XenonCodes\PHP2\Blog\Exceptions\AuthTokenNotFoundException;
use XenonCodes\PHP2\Blog\Repositories\AuthTokensRepository\AuthTokensRepositoryInterface;
use XenonCodes\PHP2\Http\Action\ActionInterface;
use XenonCodes\PHP2\Http\Auth\BearerTokenAuthentication;
use XenonCodes\PHP2\Http\Request;
use XenonCodes\PHP2\Http\Response;
use XenonCodes\PHP2\Http\SuccessfulResponse;

class LogOut implements ActionInterface
{
    public function __construct(
        private AuthTokensRepositoryInterface $authTokensRepository,
        private BearerTokenAuthentication $authentication
    ) {
    }

    /**
     * @throws AuthException
     */
    public function handle(Request $request): Response
    {
        $token = $this->authentication->getAuthTokenString($request);

        try {
            $authToken = $this->authTokensRepository->get($token);
        } catch (AuthTokenNotFoundException $exception) {
            throw new AuthException($exception->getMessage());
        }

        $authToken->setExpiresOn(new \DateTimeImmutable("now"));


        $this->authTokensRepository->save($authToken);

        return new SuccessfulResponse([
            'token' => $authToken->token()
        ]);
    }
}
