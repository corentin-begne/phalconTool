<?php
use Phalcon\Loader;

$loader = new Loader();
$loader->registerClasses([
    'Phalcon\ControllerBase' => $config->application->rootDir.'vendor/v-cult/phalcon/src/ControllerBase.php',
    'Phalcon\ModelBase' => $config->application->rootDir.'vendor/v-cult/phalcon/src/ModelBase.php',
    'Phalcon\Library' => $config->application->rootDir.'vendor/v-cult/phalcon/src/Library.php'
],true)
->registerDirs([
    $config->application->controllersDir,
    $config->application->modelsDir,
    $config->application->pluginsDir
], true);
$loader->register();
Phalcon\Library::get('loader', $config, $loader);
$loader->register();