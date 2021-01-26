<?

namespace Phalcon\Builder;
use Phalcon\Tools\Cli;
class Build extends \Phalcon\DI\Injectable
{
    public function __construct($controller, $action){        
        $this->rollup = $this->config->application->rootDir.'/node_modules/rollup/dist/bin/rollup';
        $this->uglify = $this->config->application->rootDir.'/node_modules/uglify-js-es6/bin/uglifyjs';
        $this->basePath = $this->config->application->publicDir.'js/'.(defined('MODULE')?'modules/':'');
        $this->ext = (defined('MODULE')?'m':'').'js';
        $this->format = defined('MODULE')?'es':'iife';        
        \Phalcon\Library::loadDir([$this->config->application->controllersDir]);
        $actions = explode(',', $action);        
        if(isset($controller)){
            if(isset($action)){
                foreach($actions as $action){
                    $this->run($controller, $action);
                }
            } else {
                foreach($this->getActions(ucfirst($controller).'Controller') as $action){
                    $this->run($controller, $action);
                }
            }
        } else {
            foreach(glob($this->config->application->controllersDir.'*Controller.php') as $controller){
                $controller = basename($controller, '.php');
                foreach($this->getActions($controller) as $action){
                    $this->run($controller, $action);
                }
            }
        } 
    }

    public function getActions($class){
        $result = [];
        foreach(get_class_methods($class) as $fn){
            if(strpos($fn, 'Action') !== false){
                $result[] = $fn;
            }
        }
        return $result;
    }

    private function exec($cmd){
        $result = exec($cmd.' >&1 2>&1', $r);
        if($result === false){
            return $result;
        } else {
            return implode("\n", $r);
        }
    }

    public function run($controller, $action){      
        $action = str_replace('Action', '', $action);
        if($action === 'index'){
            $action = '';
        } else {
            $action .= '/';
        }
        $controller = lcfirst(str_replace('Controller', '', $controller));
        $main = $this->basePath.$controller.'/'.$action.'main.'.$this->ext;
        $build = $this->basePath.$controller.'/'.$action.'build.'.$this->ext;
        if(!file_exists($main)){
            return false;
        }
        Cli::success($controller.' : '.(($action === '') ? 'index' : trim($action, '/')), true);
        if(file_exists($build)){
            exec('rm -f '.$build);
        }
        $result = $this->exec($this->rollup.' '.$main.' --file '.$build.' --format '.$this->format);
        if($result === false){
            Cli::error('rollup command failed');
        }
        $result .= "\n".$this->exec($this->uglify.' '.$build.' -c -o '.$build);
        if(defined('DEBUG')){
            echo "\n".trim($result)."\n\n";
        }
        if(file_exists($build)){
            echo $build."\n";
        } else {
            Cli::warning("build failed", true);
            echo "\n".trim($result)."\n\n";
        }
        //exec('cd '.$this->config->application->publicDir.'../;make '.ENV.' APP='.APP.' ACTION='.$action.' CONTROLLER='.$controller);
    }
}