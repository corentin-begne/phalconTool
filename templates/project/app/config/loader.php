<?php
use Phalcon\Loader;

$loader = new Loader();
$loader->registerClasses([
    'Phalcon\ControllerBase' => $config->application->rootDir.'vendor/v-cult/phalcon/src/ControllerBase.php',
    'Phalcon\ModelBase' => $config->application->rootDir.'vendor/v-cult/phalcon/src/ModelBase.php',
])
->registerDirs([
    $config->application->controllersDir,
    $config->application->modelsDir,
    $config->application->pluginsDir
])
->register();
