<?
use Phalcon\DI\FactoryDefault\CLI as CliDI,
Phalcon\Db\Adapter\Pdo\Mysql;
//Using the CLI factory default services container
$di = new CliDI();
// Load the configuration file (if any)
if(is_readable(APPLICATION_PATH . '/config/config.php')) {
    $config = include APPLICATION_PATH . '/config/config.php';
    $di->set('config', $config);
}
$di->set('db', function () use ($config) {
    return new Mysql(array(
        'host' => $config[ENV]->database->host,
        'username' => $config[ENV]->database->username,
        'password' => $config[ENV]->database->password,
        'dbname' => $config[ENV]->database->dbname,
        "charset" => $config[ENV]->database->charset
    ));
});