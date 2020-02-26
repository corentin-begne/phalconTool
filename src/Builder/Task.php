<?

namespace Phalcon\Builder;

use Phalcon\Text as Utils;

class Task extends \Phalcon\DI\Injectable
{
    public function __construct($task, $actions=''){        
        $source = file_get_contents(TEMPLATE_PATH.'/php/task.php');
        $name = Utils::camelize(Utils::uncamelize($task));
        $target = $this->config->application->tasksDir.$name.'Task.php';
        if(file_exists($target)){
            $source = file_get_contents($target);
        } else {
            $source = str_replace('[name]', $name, $source);
        }        
        $this->setActions($task, $actions, $source);
        file_put_contents($target, $source);        
        echo $target."\n";
    }

    public function setActions($controller, $actions, &$source){
        $modelAction = "\tpublic function [name]Action(){\n\n\t}\n\n";
        if(!empty($actions)){
            $content = '';
            foreach(explode(',', $actions) as $action){
                $content .= str_replace('[name]', lcfirst(Utils::camelize(Utils::uncamelize($action))),$modelAction);               
            }
            
            $source = str_replace("}\n}", "}\n\n".$content.'}', $source);
        }
    }
}