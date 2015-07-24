<?
use Phalcon\Builder\Model,
Phalcon\Builder\Controller,
Phalcon\Builder\Js,
Phalcon\Builder\Css,
Phalcon\Builder\Less,
Phalcon\Tools\Cli,
Phalcon\Text as Utils;
class GenerateTask extends \Phalcon\CLI\Task
{
    public function mainAction() {

    }

    public function appAction($params) {
        list($appName) = $params;
        $appPath = HOME_PATH.'apps/'.$appName;
        if(file_exists($appPath)){
            exec('cp -r '.TEMPLATE_PATH.'/project/app/* '.$appsPath);
            $content = file_get_contents($appsPath.'config/config.php');
            file_put_contents($appsPath.'config/config.php', str_replace('[app]', $appName, $content));
            Cli::success('app '.$appName.' successfully created');
        } else {
            Cli::error('app folder '.$appName.' already exists');
        }
    }

    public function projectAction($params) {
        list($appName) = $params;
        $appsPath = HOME_PATH.'apps';
        if(file_exists($appsPath)){
            exec('mkdir '.$appsPath);
            exec('cp -r '.TEMPLATE_PATH.'/project/app/* '.$appsPath);

            // create app
            $this->console->handle(array(
               'task'   => 'generate',
               'action' => 'app',
               'params' => $params 
            ));

            // create public dir
            exec('cp -r '.TEMPLATE_PATH.'/project/public '.HOME_PATH);
            $content = file_get_contents(HOME_PATH.'public/.htaccess');
            file_put_contents(HOME_PATH.'public/.htaccess', str_replace('[app]', $appName, $content));
            echo "\n";
            Cli::success('project '.$appName.' successfully created');
        } else {
            Cli::error('apps folder already exists');
        }
    }

    /**
     * generate all models
     */
    public function modelsAction() {
        $constraints = [];
        foreach($this->db->listTables($this->config[ENV]->database->dbname) as &$table){
            $model = new Model($table);           
            if(isset($model->constraints)){
                foreach($model->constraints as $name => $lines){
                    if(!isset($constraints[$name])){
                        $constraints[$name] = [];
                    }
                    $constraints[$name] = array_merge($constraints[$name], $lines);                           
                }
            }
        }     
        foreach($constraints as $name => $lines){
            $target = $this->config->application->modelsDir.$name.'.php';
            $content = file_get_contents($target);
            $content = str_replace("initialize()\n    {", "initialize()\n    {\n".implode($lines), $content);
            file_put_contents($target, $content);
        }
    }

    /**
     * generate module, action, css/js, rest, security
     * @params([params: [], options: []])
     */
    public function controllerAction($params){
        list($controller, $actions) = $params;
        new Controller($controller, $actions);
    }

    public function jsAction($params){
        list($controller, $actions) = $params;
        new Js($controller, $actions);
    }

    public function cssAction($params){
        list($controller, $actions) = $params;
        new Css($controller, $actions);
    }

    public function lessAction($params){
        list($controller, $action) = $params;
        new Less($controller, $action);
    }
}