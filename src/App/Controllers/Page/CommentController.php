<?php

namespace App\Controllers\Page;

use App\Models\Comment;
use App\Controllers\BaseController;
use App\Transformers\CommentTransformer;
use App\Exceptions\Error;

use Respect\Validation\Validator as v;
use Slim\Http\Request;
use Slim\Http\Response;

class CommentController extends BaseController {

    const DELETED = 'the comment has been deleted';

    /**
     * Get Collection of all Comments in given Page
     * @return Response
     */
    public function index(Request $request, Response $response, array $args) {
        if (!$user = $this->auth->requestUser($request)) {
            return Error::unauthorized($response);
        }
        if (!$page = $this->requestPage($args['guid'], $user)) {
            return Error::forbidden($response);
        }
        $result = $this->resources($page->comments, new CommentTransformer);
        return $this->render($response, $result);
    }

    /**
     * Create a New Comment
     * @return Response
     */
    public function create(Request $request, Response $response, array $args) {
        if (!$user = $this->auth->requestUser($request)) {
            return Error::unauthorized($response);
        }
        if (!$page = $this->requestPage($args['guid'], $user)) {
            return Error::forbidden($response);
        }
        $data = $this->getParsedBody($request);
        $validation = $this->validateCreateRequest($data);

        if ($validation->failed()) {
            return $this->render($response, $validation->getErrors(), 422);
        }
        $comment = Comment::create([
            'body' => $data['body'],
            'user_id' => $user->id,
            'page_id' => $page->id
        ]);
        $result = $this->resources($comment, new CommentTransformer);
        return $this->render($response, $result, 201);
    }

    /**
     * Delete a Comment
     * @return Response
     */
    public function delete(Request $request, Response $response, array $args) {
        if (!$user = $this->auth->requestUser($request)) {
            return Error::unauthorized($response);
        }
        if (!$page = $this->requestPage($args['guid'], $user)) {
            return Error::forbidden($response);
        }
        $page->comment($args['commentId'])->delete();
        return $this->render($response, self::DELETED, 200);
    }

    /**
     * Validation
     */
    protected function validateCreateRequest($values) {
        return $this->validator->validateArray($values, [
            'body' => v::notEmpty()
        ]);
    }

}
