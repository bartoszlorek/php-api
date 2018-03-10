<?php

namespace App\Controllers\Page;

use App\Models\User;
use App\Models\Page;
use App\Controllers\BaseController;
use App\Transformers\PageTransformer;
use App\Transformers\PageListTransformer;
use App\Exceptions\Error;

use Respect\Validation\Validator as v;
use Slim\Http\Request;
use Slim\Http\Response;

class PageController extends BaseController {

    const DELETED = 'the page has been deleted';
    const ATTACHED = 'the user has been attached';
    const DETACHED = 'the user has been detached';

    private function requestPage(string $guid, User $user) {
        $page = Page::where('guid', $guid);

        // Admin doesn't need to be attached
        if ($user->isAdmin() == false) {
            $page = $page->whereInUsers($user->id);
        }
        return $page->first();
    }

    /**
     * Get Collection (or Item) of Request User Pages
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

        $result = $this->resources($pages, new PageListTransformer);
        return $this->render($response, $result);
    }

    /**
     * Get Single Page by GUID
     * @return Response
     */
    public function show(Request $request, Response $response, array $args) {
        if (!$user = $this->auth->requestUser($request)) {
            return Error::unauthorized($response);
        }
        if (!$page = $this->requestPage($args['guid'], $user)) {
            return Error::forbidden($response);
        }
        $result = $this->resources($page, new PageTransformer);
        return $this->render($response, $result);
    }

    /**
     * Create a New Page
     * @return Response
     */
    public function create(Request $request, Response $response) {
        if (!$user = $this->auth->requestUser($request)) {
            return Error::unauthorized($response);
        }
        $data = $this->getParsedBody($request);
        $validation = $this->validateCreateRequest($data);

        if ($validation->failed()) {
            return $this->render($response, $validation->getErrors(), 422);
        }
        $page = new Page($data);
        $page->guid = 'temporary';
        $page->save();

        // ID is available only in existing record
        $page->guid = $this->auth->generateGuid($page->id);
        $page->save();

        // doesn't attach an Admin
        if ($user->isAdmin() == false) {
            $page->attachUser($user->id);
        }
        $result = $this->resources($page, new PageTransformer);
        return $this->render($response, $result, 201);
    }

    /**
     * Update a Page
     * @return Response
     */
    public function update(Request $request, Response $response, array $args) {
        if (!$user = $this->auth->requestUser($request)) {
            return Error::unauthorized($response);
        }
        if (!$page = $this->requestPage($args['guid'], $user)) {
            return Error::forbidden($response);
        }
        $data = $this->getParsedBody($request);
        $validation = $this->validateUpdateRequest($data);

        if ($validation->failed()) {
            return $this->render($response, $validation->getErrors(), 422);
        }
        // fields available to all
        $page->set($data, ['title', 'body']);

        // fields available to an Admin or Moderator
        if ($user->isCommonUser() == false) {
            $page->set($data, ['status', 'state']);
        }
        $page->save();

        if (isset($data['user'])) {
            $page->attachUser((int) $data['user']);
        }
        $result = $this->resources($page, new PageTransformer);
        return $this->render($response, $result);
    }

    /**
     * Delete a Page
     * @return Response
     */
    public function delete(Request $request, Response $response, array $args) {
        if (!$user = $this->auth->requestUser($request)) {
            return Error::unauthorized($response);
        }
        if (!$page = $this->requestPage($args['guid'], $user)) {
            return Error::forbidden($response);
        }
        $page->users()->detach();
        $page->delete();
        return $this->render($response, self::DELETED, 200);
    }

    /**
     * Attach User to the Page
     * @return Response
     */
    public function attach(Request $request, Response $response, array $args) {
        if (!$user = $this->auth->requestUser($request)) {
            return Error::unauthorized($response);
        }
        if (!$page = $this->requestPage($args['guid'], $user)) {
            return Error::forbidden($response);
        }
        $data = $this->getParsedBody($request);
        if (isset($data['user'])) {
            $page->attachUser((int) $data['user']);
        }
        return $this->render($response, self::ATTACHED, 200);
    }

    /**
     * Detach User from the Page
     * @return Response
     */
    public function detach(Request $request, Response $response, array $args) {
        if (!$user = $this->auth->requestUser($request)) {
            return Error::unauthorized($response);
        }
        if (!$page = $this->requestPage($args['guid'], $user)) {
            return Error::forbidden($response);
        }
        $data = $this->getParsedBody($request);
        if (isset($data['user'])) {
            $page->detachUser((int) $data['user']);
        }
        return $this->render($response, self::DETACHED, 200);
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
