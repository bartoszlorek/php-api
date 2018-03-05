<?php

namespace App\Controllers\User;

use App\Transformers\UserTransformer;
use Interop\Container\ContainerInterface;
use League\Fractal\Resource\Item;
use Respect\Validation\Validator as v;
use Slim\Http\Request;
use Slim\Http\Response;

class UserController {
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

    public function show(Request $request, Response $response) {
        if ($user = $this->auth->requestUser($request)) {
            $data = $this->fractal->createData(new Item($user, new UserTransformer()))->toArray();
            return $response->withJson(['user' => $data]);
        }
    }

    public function update(Request $request, Response $response) {
        if ($user = $this->auth->requestUser($request)) {
            $requestParams = $request->getParam('user');
            $validation = $this->validateUpdateRequest($requestParams, $user->id);

            if ($validation->failed()) {
                return $response->withJson(['errors' => $validation->getErrors()], 422);
            }

            $user->update([
                'email' => isset($requestParams['email']) ? $requestParams['email'] : $user->email,
                'username' => isset($requestParams['username']) ? $requestParams['username'] : $user->username,
                'password' => isset($requestParams['password'])
                ? password_hash($requestParams['password'], PASSWORD_DEFAULT)
                : $user->password,
            ]);

            $data = $this->fractal->createData(new Item($user, new UserTransformer()))->toArray();
            return $response->withJson(['user' => $data]);
        };
    }

    protected function validateUpdateRequest($values, $userId) {
        return $this->validator->validateArray($values, [
            'email' => v::optional(
                v::noWhitespace()->notEmpty()->email()
                ->existsWhenUpdate($this->db->table('users'), 'email', $userId)
            ),
            'username' => v::optional(
                v::noWhitespace()->notEmpty()
                ->existsWhenUpdate($this->db->table('users'), 'username', $userId)
            ),
            'password' => v::optional(v::noWhitespace()->notEmpty())
        ]);
    }
}
