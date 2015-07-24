<?
use Phalcon\Loader;
$loader = new Loader();
$loader->registerDirs([
    ROOT_PATH.'/tasks'
]);
if(file_exists(APPLICATION_PATH)){
   $loader->registerDirs([
        APPLICATION_PATH.'/tasks',
        APPLICATION_PATH.'/models'
    ]); 
}
$loader->registerNamespaces(['Phalcon' => ROOT_PATH.'/src'])
->register();