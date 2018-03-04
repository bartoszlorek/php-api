<?php

namespace App\Controllers;

use Slim\Http\Request;

class BaseController
{
    public function getParsedBody(Request $request)
    {
        return json_decode($request->getBody(), true);
    }
}
