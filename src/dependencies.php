<?php
// DIC configuration

use App\Exceptions\Error;
use App\Helpers\CustomArraySerializer;
use App\Middleware\OptionalAuth;
use League\Fractal\Manager;

$container = $app->getContainer();
$container->register(new \App\Services\Database\EloquentServiceProvider());
$container->register(new \App\Services\Auth\AuthServiceProvider());

$errorHandler = function ($c) {
    return new Error($c['settings']['displayErrorDetails']);
};

$container['errorHandler'] = $errorHandler;
$container['notAllowedHandler'] = $errorHandler;
$container['notFoundHandler'] = $errorHandler;
$container['phpErrorHandler'] = $errorHandler;

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
    $jws_settings['error'] = function ($response, $arguments) {
        return Error::unauthorized($response);
    };
    return new Tuupola\Middleware\JwtAuthentication($jws_settings);
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
    $manager->setSerializer(new CustomArraySerializer());
    return $manager;
};
