<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Transformers\UserTransformer;
use App\Exceptions\Error;

use Respect\Validation\Validator as v;
use Slim\Http\Request;
use Slim\Http\Response;

class UserController extends BaseController {

    /**
     * Return a Single User
     * @return Response
     */
    public function show(Request $request, Response $response) {
        if ($user = $this->auth->requestUser($request)) {
            $result = $this->resources($user, new UserTransformer);
            return $this->render($response, $result);
        }
        return Error::unauthorized($response);
    }

    /**
     * Update a User
     * @return Response
     */
    public function update(Request $request, Response $response) {
        if ($user = $this->auth->requestUser($request)) {
            $data = $this->getParsedBody($request);
            $validation = $this->validateUpdateRequest($data, $user->id);

            if ($validation->failed()) {
                return $this->render($response, $validation->getErrors(), 422);
            }
            $user->update([
                'email' => isset($data['email']) ? $data['email'] : $user->email,
                'username' => isset($data['username']) ? $data['username'] : $user->username,
                'password' => isset($data['password']) ? password_hash($data['password'],
                    PASSWORD_DEFAULT) : $user->password
            ]);
            // regenerate token after changes
            $user->token = $this->auth->generateToken($user);
            $result = $this->resources($user, new UserTransformer);
            return $this->render($response, $result);
        }
        return Error::unauthorized($response);
    }

    /**
     * Delete a User
     * @return Response
     */
    public function delete(Request $request, Response $response) {
        if ($user = $this->auth->requestUser($request)) {
            $user->pages()->detach();
            $user->delete();
            return $this->render($response, 'successfully deleted user', 200);
        }
        return Error::unauthorized($response);
    }

    /**
     * Validation
     */
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
