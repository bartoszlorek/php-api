<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\User;
use App\Transformers\UserTransformer;
use Interop\Container\ContainerInterface;
use League\Fractal\Resource\Item;
use Respect\Validation\Validator as v;
use Slim\Http\Request;
use Slim\Http\Response;

class RegisterController extends BaseController {

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

    public function register(Request $request, Response $response) {
        $userData = $this->getParsedBody($request);
        $validation = $this->validateRegisterRequest($userData);

        if ($validation->failed()) {
            return $response->withJson(['errors' => $validation->getErrors()], 422);
        }
        $user = new User($userData);
        $user->token = $this->auth->generateToken($user);
        $user->password = password_hash($userData['password'], PASSWORD_DEFAULT);
        $user->save();

        $resource = new Item($user, new UserTransformer());
        $user = $this->fractal->createData($resource)->toArray();

        return $response->withJson([
            'user' => $user
        ]);
    }

    protected function validateRegisterRequest($values) {
        return $this->validator->validateArray($values, [
            'email' => v::noWhitespace()->notEmpty()->email()
                ->existsInTable($this->db->table('users'), 'email'),
            'username' => v::noWhitespace()->notEmpty()
                ->existsInTable($this->db->table('users'), 'username'),
            'password' => v::noWhitespace()->notEmpty()
        ]);
    }

}
