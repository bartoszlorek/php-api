<?php

namespace App\Helpers;

use Slim\Http\Request;
use Slim\Http\Response;

class Json {

    public static function getParsedBody(Request $request) {
        return json_decode($request->getBody(), true);
    }

    public static function render(Response $response, $value = null, int $code = 200) {
        if ($code >= 200 && $code < 300) {
            $output = [
                'code' => $code,
                'status' => 'success',
                'data' => $value
            ];
        } else {
            $output = [
                'code' => $code,
                'status' => 'error',
                'message' => is_null($value) ? '' : $value
            ];
        }
        return $response->withJson($output, $code);
    }

}
