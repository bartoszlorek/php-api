<?php

namespace App\Controllers;

use App\Helpers\Json;
use App\Models\User;
use App\Models\Page;

use Interop\Container\ContainerInterface;
use League\Fractal\TransformerAbstract;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Slim\Http\Request;
use Slim\Http\Response;

use Illuminate\Database\Eloquent\Collection as DBCollection;
use Illuminate\Database\Eloquent\Model as DBModel;

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

    public function resources($data, TransformerAbstract $transformer) {
        $result = null;
        
        if ($data instanceof DBCollection) {
            $result = new Collection($data, $transformer);
        }
        if ($data instanceof DBModel) {
            $result = new Item($data, $transformer);
        }
        if ($result !== null) {
            return $this->fractal->createData($result)->toArray();
        }
        return array();
    }

    public function requestPage(string $guid, User $user) {
        $page = Page::where('guid', $guid);

        // Admin doesn't need to be attached
        if ($user->isAdmin() == false) {
            $page = $page->whereInUsers($user->id);
        }
        return $page->first();
    }

}
