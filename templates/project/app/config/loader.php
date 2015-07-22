<?php
use Phalcon\Loader;

$loader = new Loader();
$loader->registerDirs([
    $config->application->controllersDir,
    $config->application->modelsDir,
    $config->application->pluginsDir
])
->registerNamespaces(['Phalcon' => $config->application->rootDir.'vendor/v-cult/phalcon/src'])
->register();
