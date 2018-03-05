<?php

namespace App\Controllers;

use Interop\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class BaseController {

    protected $auth;
    protected $fractal;
    protected $validator;
    protected $db;

    public function __construct(ContainerInterface $container) {
        $this->auth = $container->get('auth');
        $this->fractal = $container->get('fractal');
        $this->validator = $container->get('validator');
        $this->db = $container->get('db');
    }

    public function getParsedBody(Request $request) {
        return json_decode($request->getBody(), true);
    }

    public function result(Response $response, $value = '', int $code = 200) {
        $isSuccess = $code >= 200 && $code < 300;
        $property = $isSuccess ? 'data' : 'message';

        return $response->withJson([
            'code' => $code,
            'status' => $isSuccess ? 'success' : 'error',
            $property => $value
        ]);
    }

}
