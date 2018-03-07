<?php

namespace App\Controllers;

use Interop\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Helpers\Json;

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

    public function render(Response $response, $value = null, $code = 200) {
        return Json::render($response, $value, $code);
    }

    public function getParsedBody(Request $request, array $defaults = array()) {
        return $this->parseArgs(Json::getParsedBody($request), $defaults);
    }

    public function parseArgs(array $args, array $defaults = array()) {
        return !empty($defaults) ? array_merge($defaults, $args) : $args;
    }

}
