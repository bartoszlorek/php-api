<?php

namespace App\Controllers\Page;

use App\Models\Page;
use App\Transformers\PageTransformer;
use Interop\Container\ContainerInterface;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Respect\Validation\Validator as valid;
use Slim\Http\Request;
use Slim\Http\Response;

class PageController
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
     * Return a all Page for a user
     */
    public function index(Request $request, Response $response, array $args)
    {
        // TODO Extract the logic of filtering pages to its own class

        $requestUserId = optional($requestUser = $this->auth->requestUser($request))->id;
        $builder = Page::query()->latest()->with(['user'])->limit(20);

        if (is_null($requestUser)) {
            return $response->withJson([], 401);
        }
        $builder->whereIn('users', $requestUserId);

        if ($limit = $request->getParam('limit')) {
            $builder->limit($limit);
        }
        if ($offset = $request->getParam('offset')) {
            $builder->offset($offset);
        }

        $pagesCount = $builder->count();
        $pages = $builder->get();

        $data = $this->fractal
            ->createData(new Collection($pages, new PageTransformer($requestUserId)))
            ->toArray();

        return $response->withJson(['pages' => $data['data'], 'pagesCount' => $pagesCount]);
    }

    /*
     *  Return a single Page
     */
    public function show(Request $request, Response $response, array $args)
    {
        $requestUserId = optional($this->auth->requestUser($request))->id;
        $page = Page::query()->where('slug', $args['slug'])->firstOrFail();

        $data = $this->fractal
            ->createData(new Item($page, new PageTransformer($requestUserId)))
            ->toArray();

        return $response->withJson(['page' => $data]);
    }

    /*
     *  Create and store new page
     */
    public function store(Request $request, Response $response)
    {
        $requestUser = $this->auth->requestUser($request);

        if (is_null($requestUser)) {
            return $response->withJson([], 401);
        }

        $this->validator->validateArray($data = $request->getParam('page'), [
            'title' => valid::notEmpty(),
            'description' => valid::notEmpty(),
            'body' => valid::notEmpty(),
        ]);

        if ($this->validator->failed()) {
            return $response->withJson(['errors' => $this->validator->getErrors()], 422);
        }

        $page = new Page($request->getParam('page'));
        $page->slug = str_slug($page->title);
        $page->users[] = $requestUser->id;
        $page->save();

        $data = $this->fractal
            ->createData(new Item($page, new PageTransformer()))
            ->toArray();

        return $response->withJson(['page' => $data]);
    }

    /*
     *  Update Page Endpoint
     */
    public function update(Request $request, Response $response, array $args)
    {
        $page = Page::query()->where('slug', $args['slug'])->firstOrFail();
        $requestUser = $this->auth->requestUser($request);

        if (is_null($requestUser)) {
            return $response->withJson([], 401);
        }
        if ($requestUser->id != $page->user_id) {
            return $response->withJson(['message' => 'Forbidden'], 403);
        }

        $params = $request->getParam('page', []);

        $page->update([
            'title' => isset($params['title']) ? $params['title'] : $page->title,
            'body' => isset($params['body']) ? $params['body'] : $page->body,
        ]);
        if (isset($params['title'])) {
            $page->slug = str_slug($params['title']);
        }

        $data = $this->fractal
            ->createData(new Item($page, new PageTransformer()))
            ->toArray();

        return $response->withJson(['page' => $data]);
    }

    /**
     * Delete Page Endpoint
     */
    public function destroy(Request $request, Response $response, array $args)
    {
        $page = Page::query()->where('slug', $args['slug'])->firstOrFail();
        $requestUser = $this->auth->requestUser($request);

        if (is_null($requestUser)) {
            return $response->withJson([], 401);
        }
        if ($requestUser->id != $page->user_id) {
            return $response->withJson(['message' => 'Forbidden'], 403);
        }
        $page->delete();

        return $response->withJson([], 200);
    }
}
