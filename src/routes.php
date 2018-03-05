<?php

use App\Controllers\Page\PageController;
use App\Controllers\Page\CommentController;
use App\Controllers\Page\FavoriteController;
use App\Controllers\Auth\LoginController;
use App\Controllers\Auth\RegisterController;
use App\Controllers\User\ProfileController;
use App\Controllers\User\UserController;
use Slim\Http\Request;
use Slim\Http\Response;

// Api Routes
$app->group('/api', function () {
    $jwtMiddleware = $this->getContainer()->get('jwt');
    $optionalAuth = $this->getContainer()->get('optionalAuth');

    // Auth Routes
    $this->post('/users', RegisterController::class . ':register')->setName('auth.register');
    $this->post('/users/login', LoginController::class . ':login')->setName('auth.login');

    // User Routes
    $this->get('/user', UserController::class . ':show')->add($jwtMiddleware)->setName('user.show');
    $this->put('/user', UserController::class . ':update')->add($jwtMiddleware)->setName('user.update');
    $this->delete('/user', UserController::class . ':delete')->add($jwtMiddleware)->setName('user.delete');

    // Profile Routes
    // $this->get('/profiles/{username}', ProfileController::class . ':show')
    //     ->add($optionalAuth)
    //     ->setName('profile.show');
    // $this->post('/profiles/{username}/follow', ProfileController::class . ':follow')
    //     ->add($jwtMiddleware)
    //     ->setName('profile.follow');
    // $this->delete('/profiles/{username}/follow', ProfileController::class . ':unfollow')
    //     ->add($jwtMiddleware)
    //     ->setName('profile.unfollow');

    // // Articles Routes
    // $this->get('/articles/feed', ArticleController::class . ':index')->add($optionalAuth)->setName('article.index');
    // $this->get('/articles/{slug}', ArticleController::class . ':show')->add($optionalAuth)->setName('article.show');
    // $this->put('/articles/{slug}',
    //     ArticleController::class . ':update')->add($jwtMiddleware)->setName('article.update');
    // $this->delete('/articles/{slug}',
    //     ArticleController::class . ':destroy')->add($jwtMiddleware)->setName('article.destroy');
    // $this->post('/articles', ArticleController::class . ':store')->add($jwtMiddleware)->setName('article.store');
    // $this->get('/articles', ArticleController::class . ':index')->add($optionalAuth)->setName('article.index');

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
    $this->logger->info("App '/' route");
    return $response->getBody()->write('api');
});
