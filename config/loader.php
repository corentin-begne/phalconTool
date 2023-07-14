<?
use Phalcon\Autoload\Loader;

$loader = new Loader();
$includes = [
    ROOT_PATH.'/tasks'
];
if(file_exists(APPLICATION_PATH)){
    array_push($includes, APPLICATION_PATH.'/tasks', APPLICATION_PATH.'/models',  APPLICATION_PATH.'/classes', APPLICATION_PATH.'/migrations');
}
$loader->setDirectories($includes)
->setNamespaces([
    'Phalcon\Builder' => ROOT_PATH.'/src/Builder',
    'Phalcon\Websocket' => ROOT_PATH.'/src/Websocket'
])
->setClasses([
    'Phalcon\ControllerBase' => ROOT_PATH.'/src/ControllerBase.php',
    'Phalcon\Tools\Cli' => ROOT_PATH.'/src/Tools/Cli.php',
    'Phalcon\Library' => ROOT_PATH.'/src/Library.php',
    'Phalcon\ModelBase' => ROOT_PATH.'/src/ModelBase.php'
])
->register();