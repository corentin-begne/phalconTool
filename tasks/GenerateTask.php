<?
use Phalcon\Builder\Controller,
Phalcon\Builder\Js,
Phalcon\Builder\Scss,
Phalcon\Builder\Sass,
Phalcon\Builder\Build,
Phalcon\Builder\Task,
Phalcon\Tools\Cli,
Phalcon\Support\Helper\Str\Camelize,
Phalcon\Support\Helper\Str\Uncamelize;

/**
 * Task managing all basic generations
 */
class GenerateTask extends Task
{
    /**
     * Object to camelize texts
     * @var \Phalcon\Support\Helper\Str\Camelize
     */
    private Camelize $camelize;
    /**
     * Object to uncamelize texts
     * @var \Phalcon\Support\Helper\Str\Uncamelize
     */
    private Uncamelize $uncamelize;

    /**
     * Init class variables
     */
    public function __construct(){
        $this->camelize = new Camelize();
        $this->uncamelize = new Uncamelize();
    }

    /**
     * Main task action (not implemented)
     * 
     * @return void
     */
    public function mainAction():void{

    }

    /**
     * Generate application
     * 
     * @param string $appName Name of the app to create
     * 
     * @return void
     */
    public function appAction(string $appName):void {
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

    /**
     * Generate project
     * 
     * @return void
     */
    public function projectAction():void {
        $appName = 'frontend';
        $appsPath = HOME_PATH.'/apps';
        if(!file_exists($appsPath)){
            exec('mkdir '.$appsPath);       
            // create public dir and init it if not exists
            $publicPath = HOME_PATH.'/public';
            if(!file_exists($publicPath)){               
                exec('mkdir '.$publicPath);     
                exec('rsync -rtv --links '.TEMPLATE_PATH.'/project/docs/ '.HOME_PATH.'/'); 
                exec('cp '.TEMPLATE_PATH.'/project/.gitignore '.HOME_PATH.'/');
                exec('cp '.TEMPLATE_PATH.'/project/README.md '.HOME_PATH.'/'); 
                exec('cp '.TEMPLATE_PATH.'/project/defaultModels.sql '.HOME_PATH.'/');                        
                exec('cp '.TEMPLATE_PATH.'/project/public/init.php '.$publicPath);
                exec('cp '.TEMPLATE_PATH.'/project/public/.htaccess '.$publicPath);
                exec('cp -r '.TEMPLATE_PATH.'/project/node_modules '.HOME_PATH.'/');                
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

    /**
     * Generate model from table
     * 
     * @param string $table Name of the table, must exists in databasee
     * 
     * @return void
     */
    public function modelAction(string $table):void{
        if(!isset($table)){
            Cli::error('missing table name');
        }
        $source = file_get_contents(TEMPLATE_PATH.'/php/model.php');
        $name = ($this->camelize)(($this->uncamelize)($table));
        $prefix = '';
        foreach(explode('_', ($this->uncamelize)($name)) as $name2){
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
     * Generate controller / actions / views
     * 
     * @param string $controller Controller name
     * @param null|string $actions=null Actions list separated by a comma or null for an empty controller
     * 
     * @return void
     */
    public function controllerAction(string $controller, null|string $actions=null):void{
        new Controller($controller, $actions);
    }

    /**
     * Generate task / actions
     * 
     * @param string $task Task name
     * @param null|string $actions='' Actions list separated by a comma or null for an empty task with main action
     * 
     * @return void
     */
    public function taskAction(string $task, null|string $actions=''):void{
        new Task($task, $actions);
    }

    /**
     * Generate Js
     * 
     * @param string $controller Controller name
     * @param null|string $actions='' Actions list separated by a comma or null for index
     * 
     * @return void
     */
    public function jsAction(string $controller, null|string $actions=''):void{
        new Js($controller, $actions);
    }

    /**
     * Generate Scss
     * 
     * @param string $controller Controller name
     * @param null|string $actions=null Actions list separated by a comma or null for index
     * 
     * @return void
     */
    public function scssAction(string $controller, null|string $actions=null):void{
        new Scss($controller, $actions);
    }

    /**
     * Generate Sass
     * 
     * @param null|string $controller=null Controller name or null for all
     * @param null|string $actions=null Actions list separated by a comma or null for all
     * 
     * @return void
     */
    public function sassAction(null|string $controller=null, null|string $actions=null):void{
        new Sass($controller, $actions);
    }

    /**
     * Generate Js builds
     * 
     * @param null|string $controller=null Controller name or null for all
     * @param null|string $actions=null Actions list separated by a comma or null for all
     * 
     * @return void
     */
    public function buildAction(null|string $controller=null, null|string $actions=null):void{
        error_reporting(0);
        new Build($controller, $actions);
    }

    /**
     * Generate forms classes
     * 
     * @param string $names Names list seperated by a comma, should correspond to a model
     * 
     * @return void
     */
    public function formAction(string $names):void{
        if(empty($names)){
            Cli::error('Form must have a name');
        }
        $path = $this->config->application->formsDir;
        if($path === ''){
            Cli::error('Form path is empty');
        }
        foreach(explode(',', $names) as $name){
            $source = file_get_contents(TEMPLATE_PATH.'/php/form.php');
            $name = ($this->camelize)(($this->uncamelize)($name));
            $target = $path.$name.'Form.php';
            if(file_exists($target)){
                $source = file_get_contents($target);
            } else {
                $source = str_replace('[name]', $name, $source);
            }        
            file_put_contents($target, $source);        
            echo $target."\n";
        }
    }
}