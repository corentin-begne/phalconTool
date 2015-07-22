<?
define('ENV', 'dev');
try {

    /**
     * Read the configuration
     */
    $config = include __DIR__ . "/../apps/".APP."/config/config.php";

    /**
     * Read auto-loader
     */
    include __DIR__ . "/../apps/".APP."/config/loader.php";

    /**
     * Read services
     */
    include __DIR__ . "/../apps/".APP."/config/services.php";

    /**
     * Handle the request
     */
    $application = new \Phalcon\Mvc\Application($di);

    echo $application->handle()->getContent();

} catch (\Exception $e) {
    echo $e->getMessage();
}
