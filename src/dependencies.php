<?php
// DIC configuration

use App\Middleware\OptionalAuth;
use League\Fractal\Manager;
use League\Fractal\Serializer\ArraySerializer;

$container = $app->getContainer();
$container->register(new \App\Services\Database\EloquentServiceProvider());
$container->register(new \App\Services\Auth\AuthServiceProvider());

$container['errorHandler'] = function ($c) {
    return new \App\Exceptions\ErrorHandler(
        $c['settings']['displayErrorDetails']
    );
};

$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler(
        $settings['path'],
        $settings['level']
    ));
    return $logger;
};

$container['jwt'] = function ($c) {
    $jws_settings = $c->get('settings')['jwt'];
    return new \Slim\Middleware\JwtAuthentication($jws_settings);
};

$container['optionalAuth'] = function ($c) {
    return new OptionalAuth($c);
};

$container['validator'] = function ($c) {
    \Respect\Validation\Validator::with('\\App\\Validation\\Rules');
    return new \App\Validation\Validator();
};

$container['fractal'] = function ($c) {
    $manager = new Manager();
    $manager->setSerializer(new ArraySerializer());
    return $manager;
};
