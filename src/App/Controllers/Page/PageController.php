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

class PageController extends BaseController {

    /**
     * Get Collection (or Item) of Request User Pages
     * @return Response
     */
    public function index(Request $request, Response $response) {
        if ($user = $this->auth->requestUser($request)) {
            $data = $this->parseArgs($request->getQueryParams(), [
                'offset' => 0,
                'limit' => 1
            ]);
            $pages = Page::whereInUsers($user->id)
                ->orderBy('updated_at', 'desc')
                ->offset($data['offset'])
                ->limit($data['limit'])
                ->get();

            $result = $this->resources($pages, new PageListTransformer);
            return $this->render($response, $result);
        }
        return Error::unauthorized($response);
    }

    /**
     * Get Single Page by GUID
     * @return Response
     */
    public function show(Request $request, Response $response, array $args) {
        if ($user = $this->auth->requestUser($request)) {
            $page = Page::where('guid', $args['guid']);

            // Admin doesn't need to be attached to this page
            if ($user->isAdmin() == false) {
                $page = $page->whereInUsers($user->id);
            }
            if (($page = $page->first()) == null) {
                return Error::forbidden($response);
            }
            $result = $this->resources($page, new PageTransformer);
            return $this->render($response, $result);
        }
        return Error::unauthorized($response);
    }

    /**
     * Create a New Page
     * @return Response
     */
    public function create(Request $request, Response $response) {
        if ($user = $this->auth->requestUser($request)) {
            $data = $this->getParsedBody($request);
            $validation = $this->validateCreateRequest($data);

            if ($validation->failed()) {
                return $this->render($response, $validation->getErrors(), 422);
            }
            $page = new Page($data);
            $page->guid = 'temporary';
            $page->save();

            // id is available only from existing record
            $page->guid = $this->auth->generateGuid($page->id);
            $page->save();

            // doesn't attach an Admin
            if ($user->isAdmin() == false) {
                $page->attachUser($user->id);
            }
            $result = $this->resources($page, new PageTransformer);
            return $this->render($response, $result, 201);
        }
        return Error::unauthorized($response);
    }

    /**
     * Update a Page
     * @return Response
     */
    public function update(Request $request, Response $response, array $args) {
        if ($user = $this->auth->requestUser($request)) {
            $page = Page::where('guid', $args['guid']);

            // Admin doesn't need to be attached to this page
            if ($user->isAdmin() == false) {
                $page = $page->whereInUsers($user->id);
            }
            if (($page = $page->first()) == null) {
                return Error::forbidden($response);
            }
            $data = $this->getParsedBody($request);
            $validation = $this->validateUpdateRequest($data);

            if ($validation->failed()) {
                return $this->render($response, $validation->getErrors(), 422);
            }
            // fields available to a Common User
            $page->set($data, ['title', 'body']);

            // fields available to an Admin or Moderator
            if ($user->isCommonUser() == false) {
                $page->set($data, ['status', 'state']);
            }
            $page->save();

            // attach new user
            if (isset($data['user'])) {
                $page->attachUser((int) $data['user']);
            }
            $result = $this->resources($page, new PageTransformer);
            return $this->render($response, $result);
        }
        return Error::unauthorized($response);
    }

    /**
     * Delete a Page
     * @return Response
     */
    public function delete(Request $request, Response $response, array $args) {
        if ($user = $this->auth->requestUser($request)) {
            $page = Page::where('guid', $args['guid']);

            // Admin doesn't need to be attached to this page
            if ($user->isAdmin() == false) {
                $page = $page->whereInUsers($user->id);
            }
            if (($page = $page->first()) == null) {
                return Error::forbidden($response);
            }
            $page->users()->detach();
            $page->delete();
            return $this->render($response, 'successfully deleted page', 200);
        }
        return Error::unauthorized($response);
    }

    /**
     * Validation
     */
    protected function validateCreateRequest($values) {
        return $this->validator->validateArray($values, [
            'title' => v::notEmpty()
        ]);
    }

    protected function validateUpdateRequest($values) {
        return $this->validator->validateArray($values, [
            'title' => v::optional(v::stringType()),
            'type' => v::optional(v::stringType()),
            'status' => v::optional(v::stringType()),
            'state' => v::optional(v::stringType()),
            'body' => v::optional(v::stringType())
        ]);
    }

}
