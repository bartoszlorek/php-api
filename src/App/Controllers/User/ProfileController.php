<?php

namespace App\Controllers\User;

use App\Models\User;
use Interop\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class ProfileController {
    protected $auth;
    protected $fractal;

    public function __construct(ContainerInterface $container) {
        $this->auth = $container->get('auth');
        $this->fractal = $container->get('fractal');
    }

    public function show(Request $request, Response $response, array $args) {
        $user = User::where('username', $args['username'])->firstOrFail();
        $requestUser = $this->auth->requestUser($request);

        return $response->withJson([
            'profile' => [
                'username' => $user->username
            ]
        ]);
    }
}
