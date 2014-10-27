<?php

namespace ETNA\Silex\Provider\Check;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;

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
