<?php
use Phalcon\Autoload\Loader;

$loader = new Loader();
$loader->setClasses([
    'Phalcon\ControllerBase' => $config->application->rootDir.'vendor/v-cult/phalcon/src/ControllerBase.php',
    'Phalcon\ModelBase' => $config->application->rootDir.'vendor/v-cult/phalcon/src/ModelBase.php',
    'Phalcon\FormBase' => $config->application->rootDir.'vendor/v-cult/phalcon/src/FormBase.php',
    'Phalcon\Library' => $config->application->rootDir.'vendor/v-cult/phalcon/src/Library.php',
    'Rest' => $config->application->rootDir.'vendor/v-cult/phalcon/src/Tools/Rest.php'
],true)
->setDirectories([
    $config->application->controllersDir,
    $config->application->modelsDir,
    $config->application->pluginsDir,
    $config->application->formsDir,
    $config->application->classesDir
], true);
$loader->register();
Phalcon\Library::get('loader', $config, $loader);
$loader->register();