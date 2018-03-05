<?php

namespace App\Controllers\Auth;

use App\Models\User;
use App\Controllers\BaseController;
use App\Transformers\UserTransformer;

use League\Fractal\Resource\Item;
use Respect\Validation\Validator as v;
use Slim\Http\Request;
use Slim\Http\Response;

/*
{
    "email": "user@mail.com",
    "password": "1234",
    "username": "John Doe"
}
*/

class RegisterController extends BaseController {

    public function register(Request $request, Response $response) {
        $userData = $this->getParsedBody($request);
        $validation = $this->validateRegisterRequest($userData);

        if ($validation->failed()) {
            return $this->result($response, $validation->getErrors(), 422);
        }
        $user = new User($userData);
        $user->token = $this->auth->generateToken($user);
        $user->password = password_hash($userData['password'], PASSWORD_DEFAULT);
        $user->save();

        $resource = new Item($user, new UserTransformer());
        $user = $this->fractal->createData($resource)->toArray();
        return $this->result($response, $user);
    }

    protected function validateRegisterRequest($values) {
        return $this->validator->validateArray($values, [
            'email' => v::noWhitespace()->notEmpty()->email()
                ->existsInTable($this->db->table('users'), 'email'),
            'password' => v::noWhitespace()->notEmpty(),
            'username' => v::notEmpty()
        ]);
    }

}
