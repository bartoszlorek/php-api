<?php

namespace App\Controllers\Auth;

use App\Models\User;
use App\Controllers\BaseController;
use App\Transformers\UserTransformer;

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
        $data = $this->getParsedBody($request);
        $validation = $this->validateRegisterRequest($data);

        if ($validation->failed()) {
            return $this->render($response, $validation->getErrors(), 422);
        }
        $user = new User($data);
        $user->token = $this->auth->generateToken($user);
        $user->password = password_hash($data['password'], PASSWORD_DEFAULT);
        $user->save();

        $result = $this->resources($user, new UserTransformer);
        return $this->render($response, $result, 201);
    }

    /**
     * Validation
     */
    protected function validateRegisterRequest($values) {
        return $this->validator->validateArray($values, [
            'email' => v::noWhitespace()->notEmpty()->email()
                ->existsInTable($this->db->table('users'), 'email'),
            'password' => v::noWhitespace()->notEmpty(),
            'username' => v::notEmpty()
        ]);
    }

}
