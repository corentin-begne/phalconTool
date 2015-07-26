<?
$includes = [
    'Phalcon\lib\api\ApiController' => $config->application->rootDir.'vendor/v-cult/phalcon/src/lib/api/ApiController.php'
];
if(!in_array('Phalcon\Tools\Rest', $loader->getClasses())){
    $includes['Phalcon\Tools\Rest'] = $config->application->rootDir.'vendor/v-cult/phalcon/src/Tools/Rest.php';
}
$loader->registerClasses($includes)
->register();