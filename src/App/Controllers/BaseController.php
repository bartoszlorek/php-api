<?php

namespace App\Controllers;

use App\Helpers\Json;

use Interop\Container\ContainerInterface;
use League\Fractal\TransformerAbstract;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Slim\Http\Request;
use Slim\Http\Response;

use Illuminate\Database\Eloquent\Collection as DBCollection;
use Illuminate\Database\Eloquent\Model;

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

    public function getResources($data, TransformerAbstract $transformer) {
        $resources = null;
        
        if ($data instanceof DBCollection) {
            $resources = new Collection($data, $transformer);
        }
        if ($data instanceof Model) {
            $resources = new Item($data, $transformer);
        }
        if ($resources !== null) {
            return $this->fractal->createData($resources)->toArray();
        }
        return array();
    }

}
