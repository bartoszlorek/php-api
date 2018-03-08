<?php

namespace App\Exceptions;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use App\Helpers\Json;

class Error {

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $handler = null) {
        if (is_null($handler)) {
            return self::notFound($response);
        }
        if (is_array($handler)) {
            return self::notAllowed($response);
        }
        if ($handler instanceof \Exception || $handler instanceof \Throwable) {
            return Json::render($response, $handler->getMessage(), $handler->getCode());
        }
        return self::internalServer($response);
    }

    public static function unauthorized(ResponseInterface $response) {
        return Json::render($response, 'Unauthorized', 401);
    }

    public static function forbidden(ResponseInterface $response) {
        return Json::render($response, 'Forbidden', 403);
    }

    public static function notFound(ResponseInterface $response) {
        return Json::render($response, 'Not Found', 404);
    }

    public static function notAllowed(ResponseInterface $response) {
        return Json::render($response, 'Method Not Allowed', 405);
    }

    public static function internalServer(ResponseInterface $response) {
        return Json::render($response, 'Internal Server Error', 500);
    }

}
