<?php

use Phalcon\CLI\Console as ConsoleApp;
error_reporting(E_ALL);
define('VERSION', '1.0.0');
/**
* Process the console arguments
*/
getOptions($argv);
$arguments = array();
foreach($argv as $k => $arg) {
    if($k == 1) {
        $arguments['task'] = $arg;
    } elseif($k == 2) {
        $arguments['action'] = $arg;
    } elseif($k >= 3) {
        $arguments['params'][] = $arg;
    }
}

// Define paths
defined('APPLICATION_PATH')
|| define('APPLICATION_PATH', realpath(dirname(__FILE__)).'/apps/'.APP);

defined('ROOT_PATH')
|| define('ROOT_PATH', realpath(dirname(__FILE__)));

defined('TEMPLATE_PATH')
|| define('TEMPLATE_PATH', realpath(dirname(__FILE__)).'/templates');


/**
 * Read auto-loader
 */
include __DIR__ . "/config/loader.php";

/**
 * Read services
 */
include __DIR__ . "/config/services.php";

//Create a console application
$console = new ConsoleApp();
$console->setDI($di);

// define global constants for the current task and action
define('CURRENT_TASK', (isset($argv[1]) ? $argv[1] : null));
define('CURRENT_ACTION', (isset($argv[2]) ? $argv[2] : null));
try {
// handle incoming arguments
    $console->handle($arguments);
}
catch (\Phalcon\Exception $e) {
    echo $e->getMessage();
    exit(255);
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