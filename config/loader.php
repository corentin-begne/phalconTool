<?
use Phalcon\Loader;
$loader = new Loader();
$includes = [
    ROOT_PATH.'/tasks'
];
if(file_exists(APPLICATION_PATH)){
    array_push($includes, APPLICATION_PATH.'/tasks', APPLICATION_PATH.'/models',  APPLICATION_PATH.'/classes', APPLICATION_PATH.'/migrations');
}
$loader->registerDirs($includes)
->registerNamespaces([
    'Phalcon\Builder' => ROOT_PATH.'/src/Builder',
    'Phalcon\Websocket' => ROOT_PATH.'/src/Websocket'
])
->registerClasses([
    'Phalcon\Tools\Cli' => ROOT_PATH.'/src/Tools/Cli.php',
    'Phalcon\Library' => ROOT_PATH.'/src/Library.php',
    'Phalcon\ModelBase' => ROOT_PATH.'/src/ModelBase.php'
])
->register();