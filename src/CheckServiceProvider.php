<?php

namespace ETNA\Silex\Provider\Check;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;

/**
 *
 */
class CheckServiceProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        $controllers->match("/check", function () use ($app) {
            return $app->json( array( "status" => "OK" ), 200);
        });

        return $controllers;
    }
}
