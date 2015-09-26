<?php
use Phalcon\Loader;

$loader = new Loader();
$loader->registerClasses([
    'Phalcon\ControllerBase' => $config->application->rootDir.'vendor/v-cult/phalcon/src/ControllerBase.php',
    'Phalcon\ModelBase' => $config->application->rootDir.'vendor/v-cult/phalcon/src/ModelBase.php',
],true)
->registerDirs([
    $config->application->controllersDir,
    $config->application->modelsDir,
    $config->application->pluginsDir
], true);
// check librairies dependencies
if(isset($config->librairies)){
    foreach($config->librairies as &$librairy){
        $file = $config->application->libDir.$librairy.'/config/loader.php';
        if(is_readable($file)){
            include $file;
        }
    }
}
$loader->register();