<?php

namespace TestCheck;

use Silex\Application;

use ETNA\Silex\Provider\Check\CheckServiceProvider;
use Silex\ServiceProviderInterface;

/**
 * Configuration principale de l'application
 */
class AppConfig implements ServiceProviderInterface
{
    /**
     * @{inherit doc}
     */
    public function register(Application $app)
    {
        $app->mount('/', new CheckServiceProvider());
    }

    /**
     *
     * @{inherit doc}
     */
    public function boot(Application $app)
    {
        $app = $app;
    }
}
