<?
define('ENV', 'dev');
try {

    /**
     * Read the configuration
     */
    $config = include __DIR__ . "/../apps/".APP."/config/config.php";
    ini_set("session.gc_maxlifetime", $config->session_lifetime);
    ini_set('session.gc_probability',1);
    ini_set('session.gc_divisor',1);
    ini_set('session.save_path', '/tmp');
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

    echo $application->handle($_SERVER['REQUEST_URI'])->getContent();

} catch (\Exception $e) {
    echo $e->getMessage();
}
