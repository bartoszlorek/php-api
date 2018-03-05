<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Transformers\UserTransformer;

use League\Fractal\Resource\Item;
use Respect\Validation\Validator as v;
use Slim\Http\Request;
use Slim\Http\Response;

class UserController extends BaseController {

    public function show(Request $request, Response $response) {
        if (!$user = $this->auth->requestUser($request)) {
            return $this->result($response, 'unauthorized', 401);
        }
        $data = $this->fractal->createData(new Item($user, new UserTransformer()))->toArray();
        return $this->result($response, $data);
    }

    public function update(Request $request, Response $response) {
        if (!$user = $this->auth->requestUser($request)) {
            return $this->result($response, 'unauthorized', 401);
        }
        $requestData = $this->getParsedBody($request);
        $validation = $this->validateUpdateRequest($requestData, $user->id);

        if ($validation->failed()) {
            return $this->result($response, $validation->getErrors(), 422);
        }
        $user->update([
            'email' => isset($requestData['email']) ? $requestData['email'] : $user->email,
            'username' => isset($requestData['username']) ? $requestData['username'] : $user->username,
            'password' => isset($requestData['password']) ? password_hash($requestData['password'],
                PASSWORD_DEFAULT) : $user->password
        ]);

        // regenerate token after changes
        $user->token = $this->auth->generateToken($user);
        $data = $this->fractal->createData(new Item($user, new UserTransformer()))->toArray();
        return $this->result($response, $data);
    }

    public function delete(Request $request, Response $response) {
        if (!$user = $this->auth->requestUser($request)) {
            return $this->result($response, 'unauthorized', 401);
        }
        $user->delete();
        return $this->result($response, '', 200);
    }

    protected function validateUpdateRequest($values, $userId) {
        return $this->validator->validateArray($values, [
            'email' => v::optional(v::noWhitespace()->notEmpty()->email()
                ->existsWhenUpdate($this->db->table('users'), 'email', $userId)
            ),
            'password' => v::optional(v::noWhitespace()->notEmpty()),
            'username' => v::optional(v::notEmpty())
        ]);
    }

}
