<?php

use Phalcon\DI\FactoryDefault;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Url;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Mvc\Model\Metadata\Memory as MetaDataAdapter;
use Phalcon\Session\Adapter\Files as SessionAdapter;
use Phalcon\Mvc\Router;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Mvc\Dispatcher;

/**
 * The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework
 */
$di = new FactoryDefault();
$di->set('config', $config);
/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->set('url', function () use ($config) {
    $url = new Url();
    $url->setBaseUri($config->application->baseUri);

    return $url;
}, true);

$di->set('router', function () use ($config) {

    $router = new Router(false);

    // check librairies dependencies
    Phalcon\Library::get('router', $config, $router);

    $router->add('/:controller/:action', [
        'controller' => 1,
        'action'     => 2
    ]);
    $router->add('/:controller/', [
        'controller' => 1,
        'action'     => 'index'
    ]);

    $router->add("/", [
        'controller' => 'index',
        'action'     => 'index'
    ]);

    return $router;
    
}, true);

/**
 * Setting up the view component
 */
$di->set('view', function () use ($config) {

    $view = new View();
    $view->setViewsDir($config->application->viewsDir);
    $view->setLayout('default');
    $view->setPartialsDir('partials');
    $view->registerEngines([
        '.phtml' => 'Phalcon\Mvc\View\Engine\Php'
    ]);

    return $view;
}, true);

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->set('db', function () use ($config) {
    return new Mysql([
        'host' => $config[ENV]->database->host,
        'username' => $config[ENV]->database->username,
        'password' => $config[ENV]->database->password,
        'dbname' => $config[ENV]->database->dbname,
        "charset" => $config[ENV]->database->charset
    ]);
});

/**
 * If the configuration specify the use of metadata adapter use it or use memory otherwise
 */
$di->set('modelsMetadata', function () {
    return new MetaDataAdapter();
});

/**
 * Start the session the first time some component request the session service
 */
$di->set('session', function () {
    $session = new SessionAdapter();
    $session->start();

    return $session;
});

$di->set('dispatcher', function() {
    $eventsManager = new EventsManager();

    $eventsManager->attach('dispatch:beforeDispatch', new SecurityPlugin());
    $eventsManager->attach('dispatch:beforeDispatch', new AssetsPlugin());

    $dispatcher = new Dispatcher();   
    $dispatcher->setEventsManager($eventsManager);
    return $dispatcher;
});