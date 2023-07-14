<?
use Phalcon\DI\FactoryDefault\CLI as CliDI,
Phalcon\Tools\Cli,
Phalcon\Db\Adapter\Pdo\Mysql;

//Using the CLI factory default services container
// Load the configuration file (if any)
$di = new CliDI();
if(is_readable(APPLICATION_PATH . '/config/config.php')) {
    $config = include APPLICATION_PATH . '/config/config.php';
    try{        
        $di->set('config', $config);
        $di->set('loader', $loader);    
        if(is_readable(APPLICATION_PATH . '/config/services_cli.php')) {
            include APPLICATION_PATH . '/config/services_cli.php';
        } else {
            $di->set('db', function () use ($config) {
                return new Mysql([
                    'host' => $config[ENV]->database->host,
                    'username' => $config[ENV]->database->username,
                    'password' => $config[ENV]->database->password,
                    'dbname' => $config[ENV]->database->dbname,
                    "charset" => $config[ENV]->database->charset
                ]);
            });
        }
    } catch(\PDOException $e) {
        Cli::error($e->getMessage());
    }    
}