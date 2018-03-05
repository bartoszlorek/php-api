<?php

namespace App\Controllers;

use Slim\Http\Request;

class BaseController {

    public function getParsedBody(Request $request) {
        return json_decode($request->getBody(), true);
    }

    public function result(Request $request, $value = '', int $code = 200) {
        $isSuccess = $code >= 200 && $code < 300;
        $property = $isSuccess ? 'data' : 'message';

        return $response->withJson([
            'code' => $code,
            'status' => $isSuccess ? 'success' : 'error',
            $property => $value
        ]);
    }
}
