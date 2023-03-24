<?php

namespace XenonCodes\PHP2\Http\Auth;

use XenonCodes\PHP2\Blog\User;
use XenonCodes\PHP2\Http\Request;

interface AuthenticationInterface
{
    // Контракт описывает единственный метод,
    // получающий пользователя из запроса
    public function user(Request $request): User;
}
