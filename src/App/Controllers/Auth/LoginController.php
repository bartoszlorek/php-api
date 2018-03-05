<?php

namespace App\Controllers\Auth;

use App\Transformers\UserTransformer;
use Interop\Container\ContainerInterface;
use League\Fractal\Resource\Item;
use Respect\Validation\Validator as valid;
use Slim\Http\Request;
use Slim\Http\Response;

class LoginController extends BaseController {
    private $auth;
    protected $fractal;
    protected $validator;
    protected $db;

    public function __construct(ContainerInterface $container) {
        $this->auth = $container->get('auth');
        $this->fractal = $container->get('fractal');
        $this->validator = $container->get('validator');
        $this->db = $container->get('db');
    }

    public function login(Request $request, Response $response) {
        $userData = $this->getParsedBody($request);
        $validation = $this->validateLoginRequest($userData);
        
        if (
            $validation->succeed() &&
            $user = $this->auth->attempt($userData['email'], $userData['password'])
        ) {
            $user->token = $this->auth->generateToken($user);
            $data = $this->fractal->createData(new Item($user, new UserTransformer()))->toArray();
            return $this->result($response, $data);
        }
        return $this->result($response, 'invalid email or password', 422);
    }

    protected function validateLoginRequest($values) {
        return $this->validator->validateArray($values, [
            'email' => valid::noWhitespace()->notEmpty(),
            'password' => valid::noWhitespace()->notEmpty()
        ]);
    }
}
