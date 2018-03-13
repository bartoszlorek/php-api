<?php

use App\Controllers\Auth\RegisterController;
use App\Controllers\Auth\LoginController;
use App\Controllers\User\UserController;
use App\Controllers\Page\PageController;
use App\Controllers\Page\CommentController;
use App\Controllers\Page\FileController;

use Slim\Http\Request;
use Slim\Http\Response;

// Api Routes
$app->group('/api', function () {
    $jwt = $this->getContainer()->get('jwt');
    $auth = $this->getContainer()->get('optionalAuth');

    // Auth Routes
    $this->post('/auth/register', RegisterController::class . ':register')->setName('auth.register');
    $this->post('/auth/login', LoginController::class . ':login')->setName('auth.login');

    // User Routes
    $this->delete('/user', UserController::class . ':delete')->add($jwt)->setName('user.delete');
    $this->get('/user', UserController::class . ':show')->add($jwt)->setName('user.show');
    $this->put('/user', UserController::class . ':update')->add($jwt)->setName('user.update');

    // Page Routes
    $this->post('/page', PageController::class . ':create')->add($jwt)->setName('page.create');
    $this->get('/page', PageController::class . ':index')->add($auth)->setName('page.index');
    
    $this->get('/page/{guid}', PageController::class . ':show')->add($auth)->setName('page.show');
    $this->put('/page/{guid}', PageController::class . ':update')->add($jwt)->setName('page.update');
    $this->delete('/page/{guid}', PageController::class . ':delete')->add($jwt)->setName('page.delete');

    $this->post('/page/{guid}/users', PageController::class . ':attach')->add($jwt)->setName('page.attach');
    $this->delete('/page/{guid}/users', PageController::class . ':detach')->add($jwt)->setName('page.detach');

    // Comments
    $this->group('/page/{guid}/comments', function () use ($jwt, $auth) {
        $this->post('', CommentController::class . ':create')->add($jwt)->setName('comment.create');
        $this->get('', CommentController::class . ':index')->add($auth)->setName('comment.index');
        $this->delete('/{commentId}', CommentController::class . ':delete')->add($jwt)->setName('comment.delete');

        // Files
        $this->post('/{commentId}/files', FileController::class . ':create')->add($jwt)->setName('file.create');
        $this->get('/{commentId}/files', FileController::class . ':index')->add($auth)->setName('file.index');
        $this->delete('/{commentId}/files/{fileId}', FileController::class . ':delete')->add($jwt)->setName('file.delete');
    });
});

// Routes
$app->get('/[{name}]', function (Request $request, Response $response, array $args) {
    return \App\Exceptions\Error::notFound($response);
});
