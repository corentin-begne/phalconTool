<?
use Phalcon\Loader;
$loader = new Loader();
$includes = [
    ROOT_PATH.'/tasks'
];
if(file_exists(APPLICATION_PATH)){
    array_push($includes, APPLICATION_PATH.'/tasks', APPLICATION_PATH.'/models');
}
$loader->registerDirs($includes);
$loader->registerNamespaces(['Phalcon' => ROOT_PATH.'/src'])
->register();