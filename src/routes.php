<?php

use App\Controllers\Auth\RegisterController;
use App\Controllers\Auth\LoginController;
use App\Controllers\User\UserController;
use App\Controllers\Page\PageController;
use App\Controllers\Page\CommentController;
use App\Controllers\Page\FavoriteController;
use App\Controllers\User\ProfileController;

use Slim\Http\Request;
use Slim\Http\Response;

// Api Routes
$app->group('/api', function () {
    $jwtMiddleware = $this->getContainer()->get('jwt');
    $optionalAuth = $this->getContainer()->get('optionalAuth');

    // Auth Routes
    $this->post('/auth/register', RegisterController::class . ':register')->setName('auth.register');
    $this->post('/auth/login', LoginController::class . ':login')->setName('auth.login');

    // User Routes
    $this->get('/user', UserController::class . ':show')->add($jwtMiddleware)->setName('user.show');
    $this->put('/user', UserController::class . ':update')->add($jwtMiddleware)->setName('user.update');
    $this->delete('/user', UserController::class . ':delete')->add($jwtMiddleware)->setName('user.delete');

    // Page Routes
    $this->post('/page', PageController::class . ':create')->add($jwtMiddleware)->setName('page.create');
    $this->get('/page', PageController::class . ':index')->add($optionalAuth)->setName('page.index');

    $this->get('/page/{guid}', PageController::class . ':show')->add($optionalAuth)->setName('page.show');
    $this->put('/page/{guid}', PageController::class . ':update')->add($jwtMiddleware)->setName('page.update');
    $this->delete('/page/{guid}', PageController::class . ':delete')->add($jwtMiddleware)->setName('page.delete');


    // // Comments
    // $this->get('/articles/{slug}/comments',
    //     CommentController::class . ':index')
    //     ->add($optionalAuth)
    //     ->setName('comment.index');
    // $this->post('/articles/{slug}/comments',
    //     CommentController::class . ':store')
    //     ->add($jwtMiddleware)
    //     ->setName('comment.store');
    // $this->delete('/articles/{slug}/comments/{id}',
    //     CommentController::class . ':destroy')
    //     ->add($jwtMiddleware)
    //     ->setName('comment.destroy');

    // // Favorite Article Routes
    // $this->post('/articles/{slug}/favorite',
    //     FavoriteController::class . ':store')
    //     ->add($jwtMiddleware)
    //     ->setName('favorite.store');
    // $this->delete('/articles/{slug}/favorite',
    //     FavoriteController::class . ':destroy')
    //     ->add($jwtMiddleware)
    //     ->setName('favorite.destroy');

    // // Tags Route
    // $this->get('/tags', function (Request $request, Response $response) {
    //     return $response->withJson([
    //         'tags' => Tag::all('title')->pluck('title'),
    //     ]);
    // });
});

// Routes
$app->get('/[{name}]', function (Request $request, Response $response, array $args) {
    return \App\Exceptions\Error::notFound($response);
});
