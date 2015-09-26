<?
$loader->registerClasses([
    'Phalcon\ApiController' => $config->application->libDir.'api/ApiController.php',
    'Phalcon\Tools\Rest' => $config->application->libDir.'../Tools/Rest.php'
], true);