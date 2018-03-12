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
    $jwtMiddleware = $this->getContainer()->get('jwt');
    $optionalAuth = $this->getContainer()->get('optionalAuth');

    // Auth Routes
    $this->post('/auth/register', RegisterController::class . ':register')->setName('auth.register');
    $this->post('/auth/login', LoginController::class . ':login')->setName('auth.login');

    // User Routes
    $this->delete('/user', UserController::class . ':delete')->add($jwtMiddleware)->setName('user.delete');
    $this->get('/user', UserController::class . ':show')->add($jwtMiddleware)->setName('user.show');
    $this->put('/user', UserController::class . ':update')->add($jwtMiddleware)->setName('user.update');

    // Page Routes
    $this->post('/page', PageController::class . ':create')->add($jwtMiddleware)->setName('page.create');
    $this->get('/page', PageController::class . ':index')->add($optionalAuth)->setName('page.index');
    
    $this->get('/page/{guid}', PageController::class . ':show')->add($optionalAuth)->setName('page.show');
    $this->put('/page/{guid}', PageController::class . ':update')->add($jwtMiddleware)->setName('page.update');
    $this->delete('/page/{guid}', PageController::class . ':delete')->add($jwtMiddleware)->setName('page.delete');

    $this->post('/page/{guid}/users', PageController::class . ':attach')->add($jwtMiddleware)->setName('page.attach');
    $this->delete('/page/{guid}/users', PageController::class . ':detach')->add($jwtMiddleware)->setName('page.detach');

    // Comments
    $this->post('/page/{guid}/comments', CommentController::class . ':create')->add($jwtMiddleware)->setName('comment.create');
    $this->get('/page/{guid}/comments', CommentController::class . ':index')->add($optionalAuth)->setName('comment.index');
    $this->delete('/page/{guid}/comments/{commentId}', CommentController::class . ':delete')->add($jwtMiddleware)->setName('comment.delete');

    // Files
    $this->post('/page/{guid}/comments/{commentId}/files', FileController::class . ':create')->add($jwtMiddleware)->setName('file.create');
    $this->get('/page/{guid}/comments/{commentId}/files', FileController::class . ':index')->add($optionalAuth)->setName('file.index');
    $this->delete('/page/{guid}/comments/{commentId}/files/{fileId}', FileController::class . ':delete')->add($jwtMiddleware)->setName('file.delete');
});

// Routes
$app->get('/[{name}]', function (Request $request, Response $response, array $args) {
    return \App\Exceptions\Error::notFound($response);
});
