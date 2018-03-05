<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Transformers\UserTransformer;

use League\Fractal\Resource\Item;
use Respect\Validation\Validator as v;
use Slim\Http\Request;
use Slim\Http\Response;

/*
{
    "email": "user@mail.com",
    "password": "1234"
}
*/

class LoginController extends BaseController {

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
        return $this->result($response, 'invalid e-mail or password', 422);
    }

    protected function validateLoginRequest($values) {
        return $this->validator->validateArray($values, [
            'email' => v::noWhitespace()->notEmpty(),
            'password' => v::noWhitespace()->notEmpty()
        ]);
    }

}
