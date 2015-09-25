<?
$includes = [
    'Phalcon\ApiController' => $config->application->libDir.'api/ApiController.php'
];
if(!in_array('Phalcon\Tools\Rest', $loader->getClasses())){
    $includes['Phalcon\Tools\Rest'] = $config->application->libDir.'../Tools/Rest.php';
}
$loader->registerClasses($includes)
->register();