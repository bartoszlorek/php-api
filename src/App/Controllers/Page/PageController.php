<?php

namespace App\Controllers\Page;

use App\Models\Page;
use App\Controllers\BaseController;
use App\Transformers\PageTransformer;
use App\Transformers\PageListTransformer;
use App\Exceptions\Error;

use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Respect\Validation\Validator as v;
use Slim\Http\Request;
use Slim\Http\Response;

/*
{
    "title": "Hello World!",
    "body": "1234"
}
*/
class PageController extends BaseController {

    /**
     * Return List of Pages
     * @return Response
     */
    public function index(Request $request, Response $response) {
        if (!$user = $this->auth->requestUser($request)) {
            return Error::unauthorized($response);
        }
        $data = $this->parseArgs($request->getQueryParams(), [
            'offset' => 0,
            'limit' => 1
        ]);
        $pages = Page::whereInUsers($user->id)
            ->orderBy('updated_at', 'desc')
            ->offset($data['offset'])
            ->limit($data['limit'])
            ->get();

        $resource = new Collection($pages, new PageListTransformer);
        $pages = $this->fractal->createData($resource)->toArray();
        return $this->render($response, $pages);
    }

    /**
     * Return a Single Page
     * @return Response
     */
    public function show(Request $request, Response $response, array $args) {
        if (!$user = $this->auth->requestUser($request)) {
            return Error::unauthorized($response);
        }
        $page = Page::where('guid', $args['guid'])
            ->whereInUsers($user->id)
            ->get();

        $resource = new Collection($page, new PageTransformer);
        $page = $this->fractal->createData($resource)->toArray();
        return $this->render($response, $page);
    }

    /**
     * Create a New Page
     * @return Response
     */
    public function create(Request $request, Response $response) {
        if (!$user = $this->auth->requestUser($request)) {
            return Error::unauthorized($response);
        }
        $pageData = $this->getParsedBody($request);
        $validation = $this->validateCreateRequest($pageData);

        if ($validation->failed()) {
            return $this->render($response, $validation->getErrors(), 422);
        }
        $page = new Page($pageData);
        $page->guid = 'temporary';
        $page->save();

        // id is available only from existing record
        $page->guid = $this->auth->generateGuid($page->id);
        $page->attachUser($user->id);
        $page->save();

        $resource = new Item($page, new PageTransformer);
        $page = $this->fractal->createData($resource)->toArray();
        return $this->render($response, $page, 201);
    }

    /**
     * Update a Page
     * @return Response
     */
    public function update(Request $request, Response $response, array $args) {
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
     * Delete a Page
     * @return Response
     */
    public function delete(Request $request, Response $response, array $args) {
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

    /**
     * Validation
     */
    protected function validateCreateRequest($values) {
        return $this->validator->validateArray($values, [
            'title' => v::notEmpty()
        ]);
    }

}
