<?

namespace Phalcon\Builder;

class Build extends \Phalcon\Mvc\User\Component
{
    public function __construct($controller, $action){
        \Phalcon\Library::loadDir([$this->config->application->controllersDir]);
        if(isset($controller)){
            if(isset($action)){
                $this->run($controller, $action);
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

    public function run($controller, $action){
        $action = strtolower(str_replace('Action', '', $action));
        if($action === 'index'){
            $action = '';
        } else {
            $action .= '/';
        }
        $controller = strtolower(str_replace('Controller', '', $controller));
        if(!file_exists($this->config->application->publicDir.'js/'.$controller.'/'.$action.'main.js')){
            return false;
        }
        \Phalcon\Tools\Cli::success($controller.' : '.(($action === '') ? 'index' : trim($action, '/')), true);
        exec('cd '.$this->config->application->publicDir.'../;make '.ENV.' APP='.APP.' ACTION='.$action.' CONTROLLER='.$controller);
    }
}