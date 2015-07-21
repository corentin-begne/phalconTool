<?

namespace Phalcon\Builder;

use Phalcon\Text as Utils;

class Controller extends \Phalcon\Mvc\User\Component
{
    public $controller;

    public function __construct($controller, $actions){        
        $this->controller = $controller;
        $source = file_get_contents(TEMPLATE_PATH.'/php/controller.php');
        $name = Utils::camelize(Utils::uncamelize($controller));
        $target = $this->config->application->controllersDir.$name.'Controller.php';
        if(file_exists($target)){
            $source = file_get_contents($target);
        } else {
            $source = str_replace('[name]', $name, $source);
        }
        $this->setActions($controller, $actions, $source);
        if(!defined(NO_VIEW)){
            exec('mkdir -p '.$this->config->application->viewsDir.$controller);
        }
        file_put_contents($target, $source);        
        echo $target."\n";
    }

    public function setActions($controller, $actions, &$source){
        $modelAction = "\tpublic function [name]Action(){\n\n\t}\n\n";
        if(isset($actions)){
            $content = '';
            foreach(explode(',', $actions) as $action){
                $content .= str_replace('[name]', lcfirst(Utils::camelize(Utils::uncamelize($action))),$modelAction);
                if(!defined(NO_VIEW) && !file_exists($this->config->application->viewsDir.$controller.'/'.$action.'.phtml')){
                    file_put_contents($this->config->application->viewsDir.$controller.'/'.$action.'.phtml', '');
                }
            }
            $source = str_replace("Controller{\n", "Controller{\n\n".$content, $source);
        }
    }
}