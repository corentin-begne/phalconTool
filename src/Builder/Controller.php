<?
namespace Phalcon\Builder;

use Phalcon\Support\Helper\Str\Camelize,
Phalcon\Support\Helper\Str\Uncamelize,
Phalcon\DI\Injectable;

/**
 * Manager controllers / actions generation
 */
class Controller extends Injectable
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
     * Generate controller empty or with action given
     * Views will be create for all actions by default except if the NO_VIEW option is set
     * 
     * @param string $controller Controller name
     * @param null|string $actions=null Actions list separated by a comma or null for an empty controller
     */
    public function __construct(string $controller, null|string $actions=null){     
        $this->camelize = new Camelize();
        $this->uncamelize = new Uncamelize();   
        $source = file_get_contents(TEMPLATE_PATH.'/php/controller.php');
        $name = ($this->camelize)(($this->uncamelize)($controller));
        $target = $this->config->application->controllersDir.$name.'Controller.php';
        if(file_exists($target)){
            $source = file_get_contents($target);
        } else {
            $source = str_replace(['[name]', '[APP]'], [$name, ((defined('TYPE') && constant('TYPE')==='app') ? ucfirst(APP).'\Base\Controller' : 'Phalcon\ControllerBase')], $source);
        }        
        if(!defined('NO_VIEW')){
            exec('mkdir -p '.$this->config->application->viewsDir.$controller);
        }
        $this->setActions($controller, $actions, $source);
        file_put_contents($target, $source);        
        echo $target."\n";
    }

    /**
     * Add action in the controller and generate view if needed
     * 
     * @param string $controller Controllre name
     * @param null|string $actions=null Actions list separated by a comma or null for an empty controller
     * @param string &$source Content of the actual controller
     * 
     * @return void
     */
    private function setActions(string $controller, null|string $actions=null, string &$source):void{
        $modelAction = "\tpublic function [name]Action(){\n\n\t}\n\n";
        if(isset($actions)){
            $content = '';
            foreach(explode(',', $actions) as $action){
                $content .= str_replace('[name]', lcfirst(($this->camelize)(($this->uncamelize)($action))),$modelAction);
                if(!defined('NO_VIEW') && !file_exists($this->config->application->viewsDir.$controller.'/'.$action.'.phtml')){
                    file_put_contents($this->config->application->viewsDir.$controller.'/'.$action.'.phtml', '');
                }
            }
            $name = (defined('TYPE') && constant('TYPE')==='app') ? ucfirst(APP)."\Base\Controller{\n" : "Phalcon\ControllerBase{\n";
            $source = str_replace($name, "$name\n".$content, $source);
        }
    }
}