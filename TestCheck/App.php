<?php

namespace TestCheck;

class App extends \Silex\Application
{
    public function __construct(array $values = array())
    {
        parent::__construct($values);

        if (!defined("APPLICATION_ENV")) {
            define("APPLICATION_ENV", getenv("APPLICATION_ENV") ?: "production");
        }

        $this->register(new AppConfig);
    }
}
