<?php

final class PA{
    /**
     * @var Phalcon\Di
     */
    static public $di;
    /**
     * @var Phalcon\Db
     */
    static public $db;
    /**
     * @var Phalcon\Mvc\Application
     */
    static public $app;
    /**
     * @var Phalcon\Config
     */
    static public $config;
    /**
     * @var Phalcon\Mvc\Router
     */
    static public $router;
    /**
     * @var Phalcon\Loader
     */
    static public $loader;
    /**
     * @var Phalcon\Mvc\Dispatcher
     */
    static public $dispatch;
    /**
     * @var Phalcon\Events\Manager
     */
    static public $em;
    /**
     * @var Phalcon\Mvc\View;
     */
    static public $view;
}
