<?php

namespace XenonCodes\PHP2\Http\Action;

use XenonCodes\PHP2\Http\Request;
use XenonCodes\PHP2\Http\Response;

interface ActionInterface
{
    public function handle(Request $request): Response;
}
