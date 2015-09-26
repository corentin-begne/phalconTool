<?
$loader->registerClasses([
    'ApiController' => $config->application->libDir.'api/ApiController.php',
    'Rest' => $config->application->libDir.'../Tools/Rest.php'
], true);