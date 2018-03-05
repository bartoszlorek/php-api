<?php

namespace App\Services\Database;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Illuminate\Database\Capsule\Manager;

class EloquentServiceProvider implements ServiceProviderInterface {

    /**
     * This method should only be used to configure services
     * and parameters. It should not get services.
     */
    public function register(Container $container) {
        $capsule = new Manager();
        $config = $container['settings']['database'];

        $capsule->addConnection([
            'driver'    => $config['driver'],
            'host'      => $config['host'],
            'database'  => $config['database'],
            'username'  => $config['username'],
            'password'  => $config['password'],
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => ''
        ]);

        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        $container['db'] = function ($c) use ($capsule) {
            return $capsule;
        };
    }

}