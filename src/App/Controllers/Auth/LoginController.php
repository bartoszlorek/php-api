<?php

namespace App\Controllers\Auth;

use App\Transformers\UserTransformer;
use Interop\Container\ContainerInterface;
use League\Fractal\Resource\Item;
use Respect\Validation\Validator as valid;
use Slim\Http\Request;
use Slim\Http\Response;

class LoginController
{
    private $auth;
    protected $fractal;
    protected $validator;
    protected $db;

    public function __construct(ContainerInterface $container)
    {
        $this->auth = $container->get('auth');
        $this->fractal = $container->get('fractal');
        $this->validator = $container->get('validator');
        $this->db = $container->get('db');
    }

    /**
     * Return token after successful login
     */
    public function login(Request $request, Response $response)
    {
        $validation = $this->validateLoginRequest($userParams = $request->getParam('user'));

        if ($validation->failed()) {
            return $response->withJson(['errors' => ['email or password' => ['is invalid']]], 422);
        }

        if ($user = $this->auth->attempt($userParams['email'], $userParams['password'])) {
            $user->token = $this->auth->generateToken($user);
            $data = $this->fractal->createData(new Item($user, new UserTransformer()))->toArray();
            return $response->withJson(['user' => $data]);
        }
        
        return $response->withJson(['errors' => ['email or password' => ['is invalid']]], 422);
    }

    protected function validateLoginRequest($values)
    {
        return $this->validator->validateArray($values, [
            'email' => valid::noWhitespace()->notEmpty(),
            'password' => valid::noWhitespace()->notEmpty(),
        ]);
    }
}
