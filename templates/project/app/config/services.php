<?php

use Phalcon\DI\FactoryDefault;
use Phalcon\Mvc\View;
use Phalcon\Url;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Mvc\Model\Metadata\Memory as MetaDataAdapter;
use Phalcon\Session\Manager;
use Phalcon\Translate\InterpolatorFactory;
use Phalcon\Translate\TranslateFactory;
use Phalcon\Session\Adapter\Stream;
use Phalcon\Mvc\Router;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Mvc\Dispatcher;

/**
 * The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework
 */
$di = new FactoryDefault();
$di->set('config', $config);
$di->set('loader', $loader);
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

    $router->add('/:controller/:action', [
        'controller' => 1,
        'action'     => 2
    ]);
    $router->add('/:controller/', [
        'controller' => 1,
        'action'     => 'index'
    ]);

    $router->add('/', [
        'controller' => 'index',
        'action'     => 'index'
    ]);

    $router->add('/:action', [
        'controller' => 'index',
        'action'     => '1'
    ]);

    // check librairies dependencies
    Phalcon\Library::get('router', $config, $router);

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
        'charset' => $config[ENV]->database->charset
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
    $session = new Manager();
    $files = new Stream(
        [
            'savePath' => '/tmp',
        ]
    );
    $session->setAdapter($files);
    $session->start();
    return $session;
});

$di->set('translation', function() use ($config, $di){
    if (file_exists($config->application->messagesDir.$di->getLang().'.php')) {
        require $config->application->messagesDir.$di->getLang().'.php';
    } else {
        require $config->application->messagesDir.'en.php';
    }
    $interpolator = new InterpolatorFactory();
    $factory      = new TranslateFactory($interpolator);
    
    return $factory->newInstance(
        'array',
        [
            'content' => $messages,
        ]
    );
});

$di->set('lang', function() use ($di){
    $lang = $di->getSession()->get('lang');
    if(!isset($lang)){
        $lang = explode('-', $di->getRequest()->getBestLanguage())[0]; 
        $di->getSession()->set('lang', $lang); 
    } 
    return $lang;
});

$di->set('dispatcher', function() {
    $eventsManager = new EventsManager();

    $security = new SecurityPlugin();
    $asset = new AssetsPlugin();
    $eventsManager->attach('dispatch:beforeDispatch', $security);
    $eventsManager->attach('dispatch:beforeDispatch', $asset);
    $eventsManager->attach('dispatch:beforeException', $security);
    $eventsManager->attach('dispatch:afterDispatch', $asset);

    $dispatcher = new Dispatcher();   
    $dispatcher->setEventsManager($eventsManager);
    return $dispatcher;
});