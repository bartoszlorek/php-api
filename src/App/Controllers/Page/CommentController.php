<?php

namespace App\Controllers\Page;

use App\Models\Comment;
use App\Models\Page;
use App\Transformers\CommentTransformer;
use Interop\Container\ContainerInterface;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Respect\Validation\Validator as valid;
use Slim\Http\Request;
use Slim\Http\Response;

class CommentController
{
    protected $auth;
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
     * Return a all Comment for a page
     */
    public function index(Request $request, Response $response, array $args)
    {
        $requestUserId = optional($this->auth->requestUser($request))->id;
        $page = Page::query()->with('comments')->where('slug', $args['slug'])->firstOrFail();

        $data = $this->fractal
            ->createData(new Collection($page->comments, new CommentTransformer($requestUserId)))
            ->toArray();

        return $response->withJson(['comments' => $data['data']]);
    }

    /**
     * Create a new comment
     */
    public function store(Request $request, Response $response, array $args)
    {
        $page = Page::query()->where('slug', $args['slug'])->firstOrFail();
        $requestUser = $this->auth->requestUser($request);

        if (is_null($requestUser)) {
            return $response->withJson([], 401);
        }

        $this->validator->validateArray($data = $request->getParam('comment'), [
            'body' => valid::notEmpty(),
        ]);
        if ($this->validator->failed()) {
            return $response->withJson(['errors' => $this->validator->getErrors()], 422);
        }

        $comment = Comment::create([
            'body' => $data['body'],
            'user_id' => $requestUser->id,
            'page_id' => $page->id,
        ]);

        $data = $this->fractal
            ->createData(new Item($comment, new CommentTransformer()))
            ->toArray();

        return $response->withJson(['comment' => $data]);
    }

    /**
     * Delete A Comment Endpoint
     */
    public function destroy(Request $request, Response $response, array $args)
    {
        $comment = Comment::query()->findOrFail($args['id']);
        $requestUser = $this->auth->requestUser($request);

        if (is_null($requestUser)) {
            return $response->withJson([], 401);
        }
        if ($requestUser->id != $comment->user_id) {
            return $response->withJson(['message' => 'Forbidden'], 403);
        }

        $comment->delete();

        return $response->withJson([], 200);
    }
}
