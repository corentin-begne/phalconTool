<?
use Phalcon\Loader;
$loader = new Loader();
$includes = [
    ROOT_PATH.'/tasks'
];
if(file_exists(APPLICATION_PATH)){
    array_push($includes, APPLICATION_PATH.'/tasks', APPLICATION_PATH.'/models');
}
$loader->registerDirs($includes)
->registerNamespaces([
    'Phalcon\Builder' => ROOT_PATH.'/src/Builder'
])
->registerClasses([
    'Phalcon\Tools\Cli' => ROOT_PATH.'/src/Tools/Cli.php',
    'Phalcon\ModelBase' => ROOT_PATH.'/src/ModelBase.php'
])
->register();