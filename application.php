#!/usr/bin/env php
<?php

use Phalcon\CLI\Console as ConsoleApp;
error_reporting(E_ALL);
define('VERSION', '1.0.0');


getOptions($argv);
$arguments = array();
foreach($argv as $k => $arg) {
    if($k == 1) {
        list($arguments['task'], $arguments['action']) = explode(':', $arg);
    } elseif($k >= 2) {
        $arguments['params'][] = $arg;
    }
}

// Define paths
defined('ROOT_PATH')
|| define('ROOT_PATH', realpath(dirname(__FILE__)));

defined('HOME_PATH')
|| define('HOME_PATH', ROOT_PATH.'/../../../');

defined('APPLICATION_PATH')
|| define('APPLICATION_PATH', HOME_PATH.'/apps/'.APP);

defined('TEMPLATE_PATH')
|| define('TEMPLATE_PATH', ROOT_PATH.'/templates');

include __DIR__ . '/config/loader.php';
include __DIR__ . '/config/services.php';

if(!isset($arguments['task'])){
    Phalcon\Tools\Cli::error('Task missing');
} else if(!isset($arguments['action'])){
    Phalcon\Tools\Cli::error('Action missing');
}

//Create a console application
$console = new ConsoleApp();
$console->setDI($di);
$di->setShared('console', $console);

// define global constants for the current task and action
define('CURRENT_TASK', (isset($arguments['task']) ? $arguments['task'] : null));
define('CURRENT_ACTION', (isset($arguments['action']) ? $arguments['action'] : null));
try {
// handle incoming arguments
    $console->handle($arguments);
}
catch (\Phalcon\Exception $e) {
    Phalcon\Tools\Cli::error($e->getMessage());
}

function getOptions(&$args){
    foreach($args as $index => &$arg){
        if(strpos($arg, '--') === 0){
            $arg = substr($arg, 2);                 
            list($name, $value) = explode('=', $arg);
            define(strtoupper($name),$value);
            unset($args[$index]);
        }
    }
    defined('APP')
    || define('APP', 'frontend');
    defined('ENV')
    || define('ENV', 'dev');
}