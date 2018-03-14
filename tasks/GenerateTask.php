<?
use Phalcon\Builder\Controller,
Phalcon\Builder\Js,
Phalcon\Builder\Css,
Phalcon\Builder\Scss,
Phalcon\Builder\Less,
Phalcon\Builder\Sass,
Phalcon\Builder\Build,
Phalcon\Builder\Task,
Phalcon\Db,
Phalcon\Builder\Migration,
Phalcon\Tools\Cli,
Phalcon\Text as Utils;
class GenerateTask extends \Phalcon\CLI\Task
{
    public function mainAction() {

    }

    public function appAction($params) {
        @list($appName) = $params;
        if(!isset($appName)){
            Cli::error('Missing app name');
        }
        $appPath = HOME_PATH.'/apps/'.$appName;
        if(!file_exists($appPath)){
            // server folder
            exec('mkdir '.$appPath);
            exec('cp -r '.TEMPLATE_PATH.'/project/app/* '.$appPath);
            $content = file_get_contents($appPath.'/config/config.php');
            file_put_contents($appPath.'/config/config.php', str_replace('[app]', $appName, $content));
            Cli::warning('Don\'t forget to modify your app config', true);
            echo $appPath."/config/config.php\n";
            // public folder
            exec('mkdir '.HOME_PATH.'/public/'.$appName);
            exec('cp -r '.TEMPLATE_PATH.'/project/public/app/* '.HOME_PATH.'/public/'.$appName.'/');
            exec('cp -r '.TEMPLATE_PATH.'/project/public/app/.htaccess '.HOME_PATH.'/public/'.$appName.'/.htaccess');
            exec('ln -s '.TEMPLATE_PATH.'/../src/lib/ '.HOME_PATH.'/public/lib');
            Cli::success('app '.$appName.' successfully created');
        } else {
            Cli::error('app folder '.$appName.' already exists');
        }
    }    

    public function projectAction() {
        $appName = 'frontend';
        $appsPath = HOME_PATH.'/apps';
        if(!file_exists($appsPath)){
            exec('mkdir '.$appsPath);       
            // create public dir and init it if not exists
            $publicPath = HOME_PATH.'/public';
            if(!file_exists($publicPath)){               
                exec('mkdir '.$publicPath);     
                //exec('cp '.TEMPLATE_PATH.'/project/.jshintrc '.HOME_PATH.'/'); 
                exec('cp -rf '.TEMPLATE_PATH.'/project/docs/* '.HOME_PATH.'/'); 
                exec('cp -rf '.TEMPLATE_PATH.'/project/docs/.* '.HOME_PATH.'/');      
                exec('cp '.TEMPLATE_PATH.'/project/public/init.php '.$publicPath);
                exec('cp '.TEMPLATE_PATH.'/project/public/.htaccess '.$publicPath);
                exec('cp -r '.TEMPLATE_PATH.'/project/public/bower.json '.$publicPath);
                exec('cp -r '.TEMPLATE_PATH.'/project/public/bower_components '.$publicPath);
                exec('cp -r '.TEMPLATE_PATH.'/project/public/Makefile '.$publicPath);
                $content = file_get_contents($publicPath.'/.htaccess');
                file_put_contents($publicPath.'/.htaccess', str_replace('[app]', $appName, $content));
            }     

            // create app
            $this->console->handle(array(
               'task'   => 'generate',
               'action' => 'app',
               'params' => [$appName] 
            ));       
            echo "\n";
            Cli::success('project '.$appName.' successfully created');
        } else {
            Cli::error('apps folder already exists');
        }
    }

    public function modelAction($params){
        list($table) = $params;
        if(!isset($table)){
            Cli::error('missing table name');
        }
        $source = file_get_contents(TEMPLATE_PATH.'/php/model.php');
        $name = Utils::camelize(Utils::uncamelize($table));
        $prefix = '';
        foreach(explode('_', Utils::uncamelize($name)) as $name2){
            $prefix .= $name2[0].$name2[1];
        }
        $content = str_replace([
            '[fields]', 
            '[name]', 
            '[realName]', 
            '[constraints]', 
            '[maps]'
        ], [
            "/**
     * @{$prefix}_id(['type':'int', 'isNull': false, 'extra': 'auto_increment', 'key': 'PRI', 'length': 11])
     */
    ".'public $id;', 
            $name, 
            $table, 
            '', 
            "'id'=>'{$prefix}_id'"], 
            $source);
        $target = $this->config->application->modelsDir.$name.'.php';
        file_put_contents($target, $content);
        echo $target."\n";
    }

    /**
     * generate module, action, css/js, rest, security
     * @params([params: [], options: []])
     */
    public function controllerAction($params){
        @list($controller, $actions) = $params;
        new Controller($controller, $actions);
    }

    public function taskAction($params){
        @list($task, $actions) = $params;
        new Task($task, $actions);
    }

    public function jsAction($params){
        @list($controller, $actions) = $params;
        new Js($controller, $actions);
    }

    public function cssAction($params){
        @list($controller, $actions) = $params;
        new Css($controller, $actions);
    }

    public function scssAction($params){
        @list($controller, $actions) = $params;
        new Scss($controller, $actions);
    }

    public function lessAction($params){
        @list($controller, $action) = $params;
        new Less($controller, $action);
    }

    public function sassAction($params){
        @list($controller, $action) = $params;
        new Sass($controller, $action);
    }

    public function buildAction($params=[]){
        error_reporting(0);
        @list($controller, $action) = $params;
        new Build($controller, $action);
    }
}