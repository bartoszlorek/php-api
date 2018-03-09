<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Transformers\UserTransformer;

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
        $data = $this->getParsedBody($request);
        $validation = $this->validateLoginRequest($data);

        if ($validation->failed()) {
            return $this->render($response, $validation->getErrors(), 422);
        }
        if ($user = $this->auth->attempt($data['email'], $data['password'])) {
            $user->token = $this->auth->generateToken($user);
            $result = $this->resources($user, new UserTransformer);
            return $this->render($response, $result);
        }
        return $this->render($response, 'invalid e-mail or password', 422);
    }

    /**
     * Validation
     */
    protected function validateLoginRequest($values) {
        return $this->validator->validateArray($values, [
            'email' => v::noWhitespace()->notEmpty()->email(),
            'password' => v::noWhitespace()->notEmpty()
        ]);
    }

}
