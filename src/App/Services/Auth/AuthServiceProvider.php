<?php

namespace App\Services\Auth;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Interop\Container\ContainerInterface;

class AuthServiceProvider implements ServiceProviderInterface {

    /**
     * This method should only be used to configure services
     * and parameters. It should not get services.
     */
    public function register(Container $container) {
        $container['auth'] = function (ContainerInterface $c) {
            return new Auth(
                $c->get('db'),
                $c->get('settings')
            );
        };
    }

}
